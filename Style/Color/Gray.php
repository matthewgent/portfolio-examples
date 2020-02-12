<?php
declare(strict_types = 1);

namespace Style\Color;

use Style\Components\Values\Color;

class Gray extends Color
{
	public function __construct(int $value, int $alpha = 1)
	{
		parent::__construct($value, $value, $value, $alpha);
	}

	public function __toString()
	{
		$hexStringArray = array();
		foreach ($this->RGB as $dec)
		{
			$hexStringArray[] = dechex($dec / 0x11);
		}
		$string = '#' . implode('', $hexStringArray);

		return $string;
	}
}
