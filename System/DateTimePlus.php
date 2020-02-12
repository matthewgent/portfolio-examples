<?php
declare(strict_types = 1);

namespace System;

use DateTime;
use DateTimeZone;

class DateTimePlus extends DateTime
{
	public function __construct($time = 'now', DateTimeZone $timezone = null)
	{
		parent::__construct($time, $timezone);
	}

	/*
	 * For safety this function returns a float value
	 * This is because if run on a 32 bit OS using an integer it will fail
	 */
	public function getJavaScriptTimestamp(): float
	{
		$timestamp = $this->getTimestamp();
		$timestamp *= 1000;
		return $timestamp;
	}
}