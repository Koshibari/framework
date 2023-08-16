<?php

namespace Koshiba\Framework\Core\Http;

class Request {
	public function __construct(public readonly array $getParams, public readonly array $postParams, public readonly array $cookies, public readonly array $files, public readonly array $server) {
	}

	public static function createFromGlobals(): static {
		return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
	}

	public function isAjax(): bool {
		return isset($this->server['HTTP_X_REQUESTED_WITH']) && $this->server['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
}