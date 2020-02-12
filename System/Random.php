<?php
declare(strict_types = 1);

namespace System;

use Errors\Exception\FatalException;
use Exception;

class Random
{
	public static function UUID(bool $dashes = true): string
	{
		$uuid = sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);

		if ($dashes === false) $uuid = str_replace('-', '', $uuid);

		return $uuid;
	}

	public static function hex(int $characters = 8): string
	{
		$minimum = '';
		$maximum = '';
		for ($c = 1; $c <= $characters; $c ++) {
			$minimum .= '1';
			$maximum .= 'F';
		}

		$minimumInteger = hexdec($minimum);
		$maximumInteger = hexdec($maximum);
		try {
			$randomInteger = random_int($minimumInteger, $maximumInteger);
		} catch (Exception $e) {
			throw new FatalException($e->getMessage());
		}

		$hexadecimal = dechex($randomInteger);

		return $hexadecimal;
	}
}