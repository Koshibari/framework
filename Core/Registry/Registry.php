<?php

namespace Koshiba\Framework\Core\Registry;

use Koshiba\Framework\Core\Abstracts\Singletone;
use Koshiba\Framework\Libs\Static\Helper;

class Registry {
	use Singletone;

	/**
	 * @var Component[]
	 */
	public static array $container = [];

	protected array $settings = [];

	protected function __construct() {
		$config         = Helper::coreConfig()->registry;
		$this->settings = $config['settings'];
		$namespace      = $this->settings['namespace'];
		foreach ($config['components'] as $name => $component) {
			/**
			 * @var $componentEntity Component
			 */
			$componentEntity = $namespace . $component;
			$this->{$name}   = $componentEntity::instance();
		}
	}

	public function getList(bool $return = true) {
		if ($return)
			return self::$container;
		dd(self::$container, annotate: "Loaded Components: ");
	}

	public function getMethods(string $name): void {
		self::$container[$name]->getMethods();
	}

	public function __get(string $name) {
		if (is_object(self::$container[$name])) {
			return self::$container[$name];
		}
		return null;
	}

	public function __set(string $name, $object): void {
		if (!isset(self::$container[$name])) {
			self::$container[$name] = new $object();
		}
	}
}