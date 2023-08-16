<?php

namespace Koshiba\Framework\Core;

use Koshiba\Core\Handlers\ErrorHandler;
use Koshiba\Core\Registry\Registry;

class App {
	public static Registry $registry;

	public function __construct() {
		self::$registry = Registry::instance();
		new ErrorHandler();
		session_start();
	}
}