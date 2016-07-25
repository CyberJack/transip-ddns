<?php

namespace CyberJack\Transip;

use Exception;
use Pimple\Container;
use Text_Template;

/**
 * Class Application
 *
 * @package CyberJack\Transip
 */
class Application
{
	/**
	 * @var Container
	 */
	protected $container;


	/**
	 * Application constructor.
	 *
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
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
		if (php_sapi_name() === 'cli')
		{
			throw new Exception('Only web mode, no cli!');
		}

		$httpMethod = $_SERVER['REQUEST_METHOD'];
		$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$routeInfo = $this->container['dispatcher']->dispatch($httpMethod, $uri);

		switch ($routeInfo[0])
		{
			case \FastRoute\Dispatcher::NOT_FOUND:
				echo (new Text_Template(APP_DIR .'/views/404.html'))->render();
				// ... 404 Not Found
				break;

			case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				$template = sprintf(APP_DIR . '/views/%s.html', $this->container['config']->show404 === true ? '404' : '405');
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
					$class = new $handler[0]($this->container);
					$class->{$handler[1]}($vars);
				}
				break;
		}
	}
}
