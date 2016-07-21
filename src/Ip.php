<?php

namespace CyberJack\Transip;

/**
 * Class Ip
 *
 * @package Cyberjack\Transip
 */
class Ip
{
	/**
	 * Get the users IP address
	 *
	 * @return string
	 */
	public static function get()
	{
		 // Init
        $ip = '0.0.0.0';
		$toCheck = ['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        // Check the ip
		if ($_SERVER)
		{
			foreach ($toCheck as $check)
			{
				if (!empty($_SERVER[$check]))
				{
					$ip = $_SERVER[$check];
					break;
				}
			}
		}
		else
		{
			foreach ($toCheck as $check)
			{
				if (getenv($check))
				{
					$ip = getenv($check);
					break;
				}
			}
		}

        // Return
        return $ip;
	}

	/**
	 * Get the hostname from the current ip address
	 *
	 * @return string
	 */
	public static function host()
	{
		return gethostbyaddr(static::get());
	}
}
