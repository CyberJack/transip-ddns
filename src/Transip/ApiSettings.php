<?php

namespace CyberJack\Transip\Transip;

use CyberJack\Transip\Config;
use Transip_ApiSettings;

/**
 * Class ApiSettings
 *
 * @package CyberJack\Transip\Transip
 */
class ApiSettings extends Transip_ApiSettings
{
	/**
	 * @var ApiSettings
	 */
	private static $instance;

	/**
	 * Get the TransIP API settings
	 *
	 * @return ApiSettings
	 */
	public static function getInstance()
	{
		if (null === static::$instance)
		{
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * ApiSettings constructor.
	 */
	public function __construct()
	{
		$config = Config::getInstance()->transip;
		static::$login = $config->login;
		static::$privateKey = file_get_contents(APP_DIR . '/config/' . $config->privateKeyFile);
	}

	/**
	 * Private clone method to prevent cloning of the instance of the Database instance.
	 *
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Private unserialize method to prevent unserializing of the Database instance.
	 *
	 * @return void
	 */
	private function __wakeup()
	{
	}
}
