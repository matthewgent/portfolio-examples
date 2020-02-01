<?php
declare(strict_types = 1);

use Errors\Exception\FatalException;

class Color
{
	protected $red;
	protected $green;
	protected $blue;
	protected $alpha;
	protected $luminance;

	public function __construct(...$RGBA)
	{
		$argumentQuantity = count($RGBA);
		if ($argumentQuantity !== 3 and $argumentQuantity !== 4) {
			throw new FatalException(
				'Color object can only accept 3 or 4 arguments.'
			);
		}
		if ($argumentQuantity === 3) {
			$RGBA[] = 1;
		}
		$this->red = $RGBA[0];
		$this->green = $RGBA[1];
		$this->blue = $RGBA[2];
		$this->alpha = $RGBA[3];

		$RGB = array_slice($RGBA, 0, 3);
		$this->luminance = self::getLuminance($RGB);
	}

	public function adjustLightness(int $value): Color
	{
		$baseRGB = $this->getRgbArray();
		$modifiedRGBA = array();

		foreach ($baseRGB as $baseValue) {
			$modifiedValue = $baseValue + $value;

			if ($modifiedValue > 0xff) {
				$modifiedValue = 0xff;
			} else if ($modifiedValue < 0x0) {
				$modifiedValue = 0x0;
			}

			$modifiedRGBA[] = $modifiedValue;
		}

		$modifiedRGBA[] = $this->alpha;

		$modifiedColor = new Color(...$modifiedRGBA);
		return $modifiedColor;
	}

	private static function getLuminance(array $RGB): float
	{
		$luminanceComponents = array();

		foreach ($RGB as $colorValue)
		{
			$unityValue = $colorValue / 0xff;

			if ($unityValue <= 0.03928) {
				$luminanceComponent = $unityValue / 12.92;
			} else {
				$luminanceComponent = (($unityValue + 0.055) / 1.055) ^ 2.4;
			}
			$luminanceComponents[] = $luminanceComponent;
		}

		$luminance = $luminanceComponents[0] * 0.2126 + $luminanceComponents[1] * 0.7152 + $luminanceComponents[2] * 0.0722;

		return $luminance;
	}

	protected function getRgbArray(): array
	{
		$array = array(
			$this->red,
			$this->green,
			$this->blue
		);

		return $array;
	}

	protected function getRgbaArray(): array
	{
		$array = array(
			$this->red,
			$this->green,
			$this->blue,
			$this->alpha
		);

		return $array;
	}

	/*
	 * Returns the minified CSS value for inclusion with declaration
	 */
	public function __toString()
	{
		$hexStringArray = array();
		if ($this->alpha === 1) {
			$rgb = $this->getRgbArray();
			foreach ($rgb as $dec) {
				$hex = dechex($dec);
				if (strlen($hex) === 1) {
					$hex = '0' . $hex;
				}
				$hexStringArray[] = $hex;
			}
			$string = '#' . implode('', $hexStringArray);
		} else {
			$rgba = $this->getRgbaArray();
			$string = 'rgba(' . implode(',', $rgba) . ')';
		}

		return $string;
	}
}
