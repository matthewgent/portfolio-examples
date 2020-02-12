<?php
declare(strict_types = 1);

namespace Request\Handler;

use Errors\Exception\FatalException;
use Errors\Response\Missing;
use Errors\Response\Server;
use Reflection\Environment;
use Request\Handler;
use System\Path;

class Page extends Handler
{
	private const TYPE_NAMESPACE = 'Page';

	public function __construct(Path $path, array $get = null, array $post = null)
	{
		parent::__construct(
			self::TYPE_NAMESPACE,
			$path,
			$get,
			$post
		);

		try {
			if (class_exists($this->requestClass, true) === false) {
				$response = new Missing;
				$response->activateHttpResponseCode();
				$this->request = $response->getPage();
			} else {
				$this->request = new $this->requestClass($this->get, $this->post);
			}
		} catch (FatalException $exception) {
			parent::standardFatalExceptionHandler($exception);
			$this->customFatalExceptionHandler($exception);
		}
	}

	protected function customFatalExceptionHandler(FatalException $exception): void
	{
		$response = new Server;
		$response->activateHttpResponseCode();
		if (Environment::isProduction() === true) {
			$request = $response->getPage();
			$request->load();
			$output = $request;
		} else {
			$output = $exception->__toString();
		}
		die($output);
	}
}