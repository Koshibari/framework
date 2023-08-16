<?php

namespace Koshiba\Framework\Libs\Static;

use Koshiba\Framework\Core\Config\Config;

class Helper {
	public static function coreConfig(): Config {
		return self::config('config');
	}

	public static function dbConfig(): Config {
		return self::config('connection');
	}

	public static function widgetConfigLocal(): Config {
		return self::config('widgets');
	}

	public static function widgetConfig(): Config {
		$widgets = getWidgets();
		$conf    = array();
		if (!empty($widgets)) {
			foreach ($widgets as $widget) {
				$wDefaultConf  = self::wDefaultConfig($widget);
				$wConf         = self::wDefaultConfig($widget, 'config');
				$wConfLocal    = self::widgetConfigLocal()->{$widget};
				$conf[$widget] = [
					'local'   => $wConfLocal,
					'default' => $wDefaultConf,
					'self'    => $wConf
				];
			}
			return new Config($conf);
		}
		return new Config([]);
	}

	protected static function wDefaultConfig(string $widget, $conf = 'default_config'): array {
		$file = path('widgets') . DS . $widget . DS . "$conf.json";
		$conf = file_get_contents($file, true);
		return json_decode($conf, true);
	}

	protected static function config(string $confName): Config {
		$configFile = path('config') . DS . "{$confName}.json";
		if (is_file($configFile)) {
			$conf = file_get_contents($configFile, true);
			$conf = json_decode($conf, true);

			return new Config($conf);
		}
		return new Config([]);
	}
}