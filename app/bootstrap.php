<?php

if(!defined('APP_DIR'))
{
	die('Error: APP_DIR is not set!');
}

define('VENDOR_DIR', __DIR__ . '/../vendor');
require_once(VENDOR_DIR . '/autoload.php');

use CyberJack\Transip\Application;
use CyberJack\Transip\Config;
use CyberJack\Transip\Database;
use FastRoute\RouteCollector;
use Pimple\Container;

$container = new Container();

// Load the application configuration
$container['config'] = function() {
	return new Config();
};

// Determine the routes
$container['dispatcher'] = function($c) {
	return FastRoute\simpleDispatcher(
		function (RouteCollector $r) use ($c){
			$config = $c['config'];
			if ($config->enableIndex === true)
			{
				$r->addRoute('GET', '/', [\CyberJack\Transip\Controllers\IndexController::class, 'index']);
			}
			$r->addRoute('POST', '/', [\CyberJack\Transip\Controllers\UpdateController::class, 'update']);
		}
	);
};

// Initialize the database connection
$container['database'] = function() {
	return new Database();
};

// Start the application
try
{
	$app = new Application($container);
}
catch (Exception $e)
{
	die($e->getMessage());
}
