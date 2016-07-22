<?php

namespace CyberJack\Transip\Controllers;

use Pimple\Container;

/**
 * Class Controller
 *
 * @package CyberJack\Transip\Controllers
 */
abstract class Controller
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Controller constructor.
	 *
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}
}
