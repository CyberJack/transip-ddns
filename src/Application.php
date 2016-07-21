<?php

namespace CyberJack\Transip;

use Exception;
use FastRoute\Dispatcher\GroupCountBased;
use Text_Template;

/**
 * Class Application
 *
 * @package CyberJack\Transip
 */
class Application
{
	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var GroupCountBased
	 */
	protected $dispatcher;


	/**
	 * Application constructor.
	 *
	 * @param GroupCountBased $dispatcher
	 * @param Config $config
	 */
	public function __construct(GroupCountBased $dispatcher, Config $config)
	{
		$this->config = $config;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Run the application
	 *
	 * @throws Exception
	 * @return void;
	 */
	public function run()
	{
		// Fetch method and URI from somewhere
		if (php_sapi_name() === "cli")
		{
			throw new Exception('Only web mode, no cli!');
		}

		$httpMethod = $_SERVER['REQUEST_METHOD'];
		$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

		switch ($routeInfo[0])
		{
			case \FastRoute\Dispatcher::NOT_FOUND:
				echo (new Text_Template(APP_DIR .'/views/404.html'))->render();
				// ... 404 Not Found
				break;

			case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				$template = sprintf(APP_DIR . '/views/%s.html', $this->config->use405 === true ? '405' : '404');
				echo (new Text_Template($template))->render();
				$allowedMethods = $routeInfo[1];
				// ... 405 Method Not Allowed
				break;

			case \FastRoute\Dispatcher::FOUND:
				$handler = $routeInfo[1];
				$vars = $routeInfo[2];

				if(!is_array($handler))
				{
					throw new Exception('Invalid route configuration!');
				}

				if(is_array($handler))
				{
					$class = new $handler[0]();
					$class->{$handler[1]}($vars);
				}
				break;
		}
	}
}
