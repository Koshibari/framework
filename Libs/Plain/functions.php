<?php

use Koshiba\Core\Abstracts\Widget;
use Koshiba\Libs\Static\DataUtils;

function dd($data, bool|int $strict = false, bool $return = false, string $annotate = ''): ?string {
	if (empty($data)) {
		return -1;
	}
	if (is_array($data)) {
		if ($return) {
			return DataUtils::unifyData($data, $strict, $return, $annotate);
		}
		DataUtils::unifyData($data, $strict, $return, $annotate);
		return null;
	}
	if (is_object($data)) {
		if ($return) {
			return DataUtils::unifyDataObject($data, $strict, $return, $annotate);
		}
		DataUtils::unifyDataObject($data, $strict, $return, $annotate);
		return null;
	}

	if ($return) {
		return DataUtils::unifyString($data, $return, $annotate);
	}
	DataUtils::unifyString($data, $return, $annotate);
	return null;
}

function path(string $location, string $module = null): string {
	$location = strtolower($location);
	$module   = in_array($module, getModules()) ? MODULE . DS . $module : APP;
	return match ($location) {
		'web' => WEB,
		'app' => APP,
		'vendor' => VENDOR,
		'libs' => VENDOR . DS . 'Libs',
		'core' => VENDOR . DS . 'Core',
		'widgets' => VENDOR . DS . "Widgets",
		'controller' => $module . DS . 'Controllers',
		'model' => $module . DS . 'Models',
		'view' => $module . DS . 'Views',
		'config' => ROOT . DS . 'config',
		'cache' => ROOT . DS . 'tmp' . DS . 'cache',
		'log' => ROOT . DS . 'tmp' . DS . 'log',
		default => ROOT,
	};
}

function getModules(): array {
	$modules = scandir(MODULE);
	unset($modules[0]);
	unset($modules[1]);
	if (!empty($modules)) {
		return $modules;
	}
	return [];
}

function getWidgets(): array {
	$widgets     = scandir(path('widgets'));
	$widgetsReal = [];
	unset($widgets[0]);
	unset($widgets[1]);

	if (!empty($widgets)) {
		foreach ($widgets as $widget) {
			if (widgetReal($widget)) {
				$widgetsReal[] = $widget;
			}
		}
		return $widgetsReal;
	}

	return [];
}

function getWidgetNamespace(string $widgetName): string {
	$widgetMask = Koshiba::WIDGET_MASK->value;
	return sprintf($widgetMask, $widgetName, $widgetName);
}

function widgetReal(string $widgetName): bool {
	$namespace = getWidgetNamespace($widgetName);

	$widget = scandir(functions . phppath('widgets') . $widgetName);
	if (!in_array(Koshiba::WIDGET_DEFAULT_CONFIG->value . Koshiba::WIDGET_CONFIG_EXT->value, $widget))
		return false;
	if (!in_array(Koshiba::WIDGET_CONFIG->value . Koshiba::WIDGET_CONFIG_EXT->value, $widget))
		return false;
	if (!in_array("{$widgetName}.php", $widget))
		return false;
	if (!class_exists($namespace)) {
		return false;
	}
	if (!is_subclass_of($namespace, Widget::class)) {
		return false;
	}

	return true;
}

function redirect(string $http = ''): void {
	$request = Request::createFromGlobals();
	if ($http != '') {
		$redirect = $http;
	} else {
		$redirect = $request->server['HTTP_REFERER'] ?? '/';
	}

	header("Location: $redirect");
	exit;
}