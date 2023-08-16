<?php

namespace Koshiba\Framework\Core\Http;

use Exception;
use Koshiba\Core\Abstracts\Controller;
use Koshiba\Framework\Core\Router;
use ReflectionMethod;

class Kernel {

	public static string $uri;

	public static array $route;

	protected static string $CONTROLLER_MASK = "\\%s\\Controllers\\%sController";

	public function handle(Request $request): Response {
		self::$uri = rtrim($request->server['REQUEST_URI'], '/');

		return $this->dispatch($request);
	}

	protected function dispatch(Request $request): ?Response {
		if (Router::matchRoute(self::$uri)) {
			self::$route    = Router::route();
			$controllerName = self::$route['controller'];
			$action         = self::$route['action'];
			$prefix         = self::$route['prefix'];
			$controller     = sprintf(self::$CONTROLLER_MASK, ucfirst($prefix), $controllerName);

			if (class_exists($controller)) {
				/**
				 * @var $controllerEntity Controller
				 */
				$controllerEntity = new $controller(self::$route);

				if (method_exists($controllerEntity, $action)) {
					$reflection = new ReflectionMethod($controllerEntity, $action);
					if ($reflection->isPublic()) {
						$controllerEntity->setRequest($request);
						$controllerEntity->{$action}();
						$view = $controllerEntity->getView();
						return new Response($view['layout']);
					}
					throw new Exception("Action <b>$controllerName::$action</b> is not public", 404);
				}
				throw new Exception("Action <b>$controllerName::$action</b> does not exists", 404);
			}
			throw new Exception("Controller <b>$controller</b> not found", 404);
		} else {
			throw new Exception("Page not found", 404);
			#return Response::_404();
		}
	}


}