<?php

namespace CyberJack\Transip;

use stdClass;
use Exception;

/**
 * Class Config
 *
 * @package CyberJack\Transip
 */
class Config
{
	/**
	 * @var stdClass
	 */
	protected $config;


	/**
	 * Config constructor.
	 */
	public function __construct()
	{
		if (!file_exists(APP_DIR . '/config/config.php'))
		{
			throw new Exception('Config file "config.php" does not exist in the app config directory!');
		}
		$this->config = include APP_DIR . '/config/config.php';
		$this->validate();
	}

	/**
	 * Validate the configuration
	 *
	 * @return bool
	 */
	protected function validate()
	{
		// @todo: Validate the configuration!
	}

	/**
	 * Get a config value
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
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
	public function __set($name, $value)
	{
		$this->config->{$name} = $value;
	}

	/**
	 * Check if a config setting has been set
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->config->{$name});
	}
}
