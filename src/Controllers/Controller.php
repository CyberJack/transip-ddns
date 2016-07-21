<?php

namespace CyberJack\Transip\Controllers;

use CyberJack\Transip\Config;

/**
 * Class Controller
 *
 * @package CyberJack\Transip\Controllers
 */
abstract class Controller
{
	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * Controller constructor.
	 */
	public function __construct()
	{
		$this->config = Config::getInstance();
	}
}
