<?php

if(!defined('APP_DIR'))
{
	die('Error: APP_DIR is not set!');
}

define('VENDOR_DIR', __DIR__ . '/../vendor');
require_once(APP_DIR . '/../vendor/autoload.php');

use CyberJack\Transip\Config;
use CyberJack\Transip\Application;
use FastRoute;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher\GroupCountBased;

if (!function_exists('hash_equals'))
{
	function hash_equals($str1, $str2)
	{
		if (strlen($str1) != strlen($str2))
		{
			return false;
		}
		else
		{
			$res = $str1 ^ $str2;
			$ret = 0;
			for ($i = strlen($res) - 1; $i >= 0; $i--)
				$ret |= ord($res[$i]);
			return !$ret;
		}
	}
}

// Load the application configuration
$config = Config::getInstance();

// Determine the routes
$dispatcher = FastRoute\simpleDispatcher(
	function (RouteCollector $r) use ($config)
	{
		if ($config->enableIndex === true)
		{
			$r->addRoute('GET', '/', [\CyberJack\Transip\Controllers\IndexController::class, 'index']);
		}
		$r->addRoute('POST', '/', [\CyberJack\Transip\Controllers\UpdateController::class, 'update']);
	}
);

// Start the application
try
{
	$app = new Application($dispatcher, $config);
}
catch (Exception $e)
{
	die($e->getMessage());
}
