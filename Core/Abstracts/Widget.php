<?php

namespace Koshiba\Framework\Core\Abstracts;

use Koshiba\Framework\Core\App;
use Koshiba\Framework\Core\Registry\Registry;
use Koshiba\Framework\Libs\Static\Helper;

abstract class Widget {
	protected string $widgetName;

	protected array $config;

	protected array $options = [];

	protected Registry $registry;

	public function __construct() {
		$this->widgetName = (new \ReflectionClass($this))->getShortName();
		if (!widgetReal($this->widgetName)) {
			throw new \Exception("Widget: <b>{$this->widgetName}</b> configured badly!", 500);
		}
		$this->registry = App::$registry;
		$this->config   = Helper::widgetConfig()->{$this->widgetName};
	}

	private function isOptionReal($optionName, $passedOption): bool {
		$options = $this->config['self']['params'];
		if (!isset($options[$optionName])) {
			return false;
		}

		$option = $options[$optionName];

		if ($option === "any:str") {
			return true;
		}

		if ($option === "any:str?") {
			return true;
		}

		if ($option === "any:int") {
			if (is_int($passedOption)) {
				return true;
			} else {
				return false;
			}
		}

		if ($option === "any:int?") {
			if (is_int($passedOption) || $passedOption === false) {
				return true;
			} else {
				return false;
			}
		}

		if (is_array($option)) {
			foreach ($option as $item) {
				if ($passedOption == $item) {
					return true;
				}
			}
			return false;
		}

		return false;
	}

	protected function options(array $options): void {
		$this->options = $options;
	}

	protected function getOption(string $optionName): mixed {
		$resOption = $this->options[$optionName] ?? null;
		if ($resOption === null) {
			$resOption = $this->config['local'][$optionName] ?? $this->config['default'][$optionName];
		}
		if ($this->isOptionReal($optionName, $resOption)) {
			return $resOption;
		}

		$type = gettype($resOption);
		throw new \Exception("Option: $optionName | Value: $resOption | Type: $type | Can't be passed as option", 500);
	}
}