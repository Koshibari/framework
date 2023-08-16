<?php

namespace Koshiba\Framework\Libs\Static;

enum ConstHelper: string {
	case WIDGET_MASK = "\Koshiba\Widgets\%s\%s";
	case WIDGET_CONFIG = "config";
	case WIDGET_DEFAULT_CONFIG = "default_config";
	case WIDGET_CONFIG_EXT = ".json";
	case ERROR_HANDLER_LOG = "handled.log";
	case DEBUG = "1";

	public function int(): int {
		return intval($this->value);
	}

	public function bool(): bool {
		return boolval(intval($this->value));
	}
}