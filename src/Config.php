<?php

namespace CyberJack\Transip;

/**
 * Class Config
 *
 * @package CyberJack\Transip
 */
class Config
{
	/**
	 * @var Config
	 */
	private static $instance;

	/**
	 * @var stdClass
	 */
	protected $config;


	/**
	 * Get the application configuration
	 *
	 * @return Config
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
	 * Protected constructor to prevent creating a new instance of the
	 * Config via the `new` operator from outside of this class.
	 */
	protected function __construct()
	{
		if (!file_exists(APP_DIR . '/config/config.php'))
		{
			throw new Exception('Config file "config.php" does not exist in the app config directory!');
		}
		$this->config = include APP_DIR . '/config/config.php';

		// @todo: Validate the configuration!
	}

	/**
	 * Private clone method to prevent cloning of the instance of the Config instance.
	 *
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Private unserialize method to prevent unserializing of the Config instance.
	 *
	 * @return void
	 */
	private function __wakeup()
	{
	}

	/**
	 * Validate the configuration
	 *
	 * @return bool
	 */
	protected function _validate()
	{

	}

	/**
	 * Get a config value
	 *
	 * @param string $name
	 * @return mixed
	 */
	function __get($name)
	{
		return (isset($this->config->{$name}) ? $this->config->{$name} : null);
	}

	/**
	 * Set/Update a config value
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	function __set($name, $value)
	{
		$this->config->{$name} = $value;
	}

	/**
	 * Check if a config setting has been set
	 *
	 * @param string $name
	 * @return bool
	 */
	function __isset($name)
	{
		return isset($this->config->{$name});
	}
}
