<?php

namespace Koshiba\Framework\Core;

use Koshiba\Framework\Core\Handlers\ErrorHandler;
use Koshiba\Framework\Core\Registry\Registry;

class App {
	public static Registry $registry;

	public function __construct() {
		self::$registry = Registry::instance();
		new ErrorHandler();
		session_start();
	}
}