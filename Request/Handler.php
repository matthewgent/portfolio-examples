<?php
declare(strict_types = 1);

namespace Request;

use Errors\Exception\FatalException;
use Reflection\Environment;
use Email\ProblemAlert;
use System\Path;

abstract class Handler
{
	private $typeNamespace;
	private $path;

	protected $get;
	protected $post;
	protected $requestClass;
	protected $request;

	protected function __construct(
		string $typeNamespace,
		Path $path,
		array $get = null,
		array $post = null
	) {
		register_shutdown_function(array($this, 'shutDownHandler'));

		$this->typeNamespace = $typeNamespace;
		$this->path = $path;

		$this->get = $get;
		$this->post = $post;
		$this->requestClass = $this->path->toClass();
	}

	public function shutDownHandler(): void
	{
		$lastError = error_get_last();
		if ($lastError !== null and $lastError['type'] === E_ERROR) {
			error_clear_last();
			ob_clean();
			$exception = new FatalException($lastError['message']);
			self::standardFatalExceptionHandler($exception);
			$this->customFatalExceptionHandler($exception);
		}
	}

	protected static function standardFatalExceptionHandler(FatalException $exception): void
	{
		if (Environment::isProduction() === true) {
			error_log($exception->__toString());

			$emailAlert = new ProblemAlert($exception);
			@$emailAlert->send();
		}
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	abstract protected function customFatalExceptionHandler(FatalException $exception): void;
}