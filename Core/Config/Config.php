<?php

namespace Koshiba\Framework\Core\Config;


class Config {
	public readonly array $response;
	public readonly array $framework;
	public readonly array $app;

	private array $dynamic = [];

	public function __construct(array $json) {
		foreach ($json as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function __set(string $name, $value): void {
		$this->dynamic[$name] = $value;
	}

	public function __get(string $name) {
		return $this->dynamic[$name] ?? [];
	}

	public function __isset(string $name): bool {
		return isset($this->dynamic[$name]);
	}
}
