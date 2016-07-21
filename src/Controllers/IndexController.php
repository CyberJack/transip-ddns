<?php

namespace CyberJack\Transip\Controllers;

use CyberJack\Transip\Ip;
use Text_Template;

/**
 * Class IndexController
 *
 * @package CyberJack\Transip\Controllers
 */
class IndexController extends Controller
{
	/**
	 * Show the application index page
	 *
	 * @return void
	 */
	public function index()
	{
		$template = new Text_Template(APP_DIR . '/views/index.html');
		$template->setVar([
			'ip' => Ip::get(),
			'host' => Ip::host()
		]);
		echo $template->render();
	}
}
