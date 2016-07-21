<?php

namespace CyberJack\Transip;

class Crypt
{
	/**
	 * Compare two strings in constant time to avoid timing attacks
	 *
	 * @param  string $expected
	 * @param  string $actual
	 * @return boolean
	 */
	public static function compareStrings($expected, $actual)
	{
		$expected = (string)$expected;
		$actual = (string)$actual;
		$lenExpected = strlen($expected);
		$lenActual = strlen($actual);
		$len = min($lenExpected, $lenActual);
		$result = 0;
		for ($i = 0; $i < $len; $i++)
		{
			$result |= ord($expected[$i]) ^ ord($actual[$i]);
		}
		$result |= $lenExpected ^ $lenActual;
		return ($result === 0);
	}
}
