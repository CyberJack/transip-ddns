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
		$this->load();
	}

	/**
	 * Load the configuration
	 *
	 * @return void
	 */
	protected function load()
	{
		$config = include APP_DIR . '/config/config.php';
		$this->validate($config);

		// Convert the config to an object
		$this->config = (object)$config;

		// Load the TransIP private key
		$this->config->transip = (object)$config['transip'];
		$this->config->transip->privateKey = trim(file_get_contents(APP_DIR . '/config/'. $config['transip']['privateKeyFile']));

		// convert all client applications to objects
		$this->config->applications = new stdClass();
		foreach($config['applications'] as $key => $application)
		{
			$this->config->applications->{$key} = (object)$application;
		}

		var_dump($this->config);
	}

	/**
	 * Validate the configuration
	 *
	 * @param array $config
	 * @return bool
	 */
	protected function validate($config)
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
