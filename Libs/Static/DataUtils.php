<?php

namespace Koshiba\Framework\Libs\Static;

class DataUtils {

	public static string $pre_open = '<pre class="col-12" style="color: #8e1652;background: #f3bcc0; border-radius: 2px; padding: 16px">';

	protected static function parseAnnotate(string &$annotate): void {
		$annotate = $annotate !== "" ? "<div style='color: #ffffff'><b>{$annotate}</b></div>" : "";
	}

	protected static function parseData(array|object &$data, bool $strict): void {
		$data = $strict ? json_encode($data, JSON_PRETTY_PRINT) : $data;
	}

	public static function unifyData(array $data, bool $strict, bool $return, string $annotate): ?string {
		self::parseData($data, $strict);
		self::parseAnnotate($annotate);
		if (!$return) {
			echo self::$pre_open;
			echo $annotate;
			print_r($data);
			echo '</pre>';
			return null;
		} else {
			return self::$pre_open . $annotate . print_r($data, 1) . '</pre>';
		}
	}

	public static function unifyDataObject(object $data, bool $strict, bool $return, string $annotate): ?string {
		self::parseData($data, $strict);
		self::parseAnnotate($annotate);
		if (!$return) {
			echo self::$pre_open;
			echo $annotate;
			print_r($data);
			echo '</pre>';
			return null;
		} else {
			return self::$pre_open . $annotate . print_r($data, 1) . '</pre>';
		}
	}

	public static function unifyString(string $data, bool $return, string $annotate): ?string {
		self::parseAnnotate($annotate);
		if (!$return) {
			echo self::$pre_open;
			echo $annotate;
			echo $data;
			echo '</pre>';
			return null;
		} else {
			return self::$pre_open . $annotate . $data . '</pre>';
		}
	}
}