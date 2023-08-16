<?php

namespace Koshiba\Framework\Core\Http;

use Koshiba\Framework\Libs\Static\Helper;

class Response {

	private static array $config;

	public function __construct(private readonly ?string $content = '', private readonly int $status = 200, private readonly array $headers = [],) {
		self::$config = Helper::coreConfig()->response;
	}

	public function send(): void {
		http_response_code($this->status);
		echo $this->content;
	}

	public static function _404(): Response {
		$err  = self::getError(404);
		$data = file_get_contents(Response . phppath($err['link']));
		return new self($data, 404);
	}

	protected static function getError(int $code): array {
		if (!isset(self::$config))
			self::$config = Helper::coreConfig()->response;

		if (isset(self::$config['errors'])) {
			$errors = self::$config['errors'];

			if (isset($errors[$code]) && isset($errors[$code]['file']) && isset($errors[$code]['link'])) {
				return $errors[$code];
			}
			throw new \Exception("Error {$code} was not implemented! Contact Author");
		}
		throw new \Exception("Errors config misses! Contact Author");
	}
}