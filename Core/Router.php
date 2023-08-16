<?php

namespace Koshiba\Framework\Core;

class Router {
	protected static array $routes = [];
	protected static array $route  = [];

	public static function add(string $regex, array $route = [
		'controller' => '',
		'action'     => '',
		'prefix'     => ''
	]) {
		if (array_key_exists("controller", $route) && preg_match("#Controller#i", $route['controller'])) {
			$route['controller'] = str_replace("Controller", "", $route['controller']);
		}
		self::$routes[$regex] = $route;
	}

	public static function routes(): array {
		return self::$routes;
	}

	public static function route(): array {
		return self::$route;
	}

	public static function matchRoute(string $uri): bool {
		$uri = self::removeQueryString($uri);
		foreach (self::$routes as $pattern => $route) {
			if (preg_match("#$pattern#i", $uri, $matches)) {
				foreach ($matches as $key => $value) {
					if (is_string($key)) {
						$route[$key] = $value;
					}
				}
				if (!isset($route['prefix']) || $route['prefix'] === '') {
					$route['prefix'] = 'app';
				}
				if ($route['action'] === '') {
					$route['action'] = 'index';
				}
				$route['controller'] = self::upperCamelCase($route['controller']);
				$route['action']     = self::lowerCamelCase($route['action']);
				$route['prefix']     = self::lowerCamelCase($route['prefix']);
				self::$route         = $route;
				return true;
			}
		}
		return false;
	}

	protected static function upperCamelCase($text): string {
		$text = str_replace('-', " ", $text);
		$text = ucwords($text);
		return str_replace(" ", "", $text);
	}

	protected static function lowerCamelCase($text): string {
		$text = str_replace('-', " ", $text);
		$text = ucwords($text);
		$text = str_replace(" ", "", $text);
		return lcfirst($text);
	}

	protected static function removeQueryString($url): string {
		if ($url) {
			$params = explode('&', $url, 2);
			if (!str_contains($params[0], '=')) {
				return rtrim($params[0], '/');
			} else {
				return '';
			}
		}

		return $url;
	}
}
