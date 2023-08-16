<?php

namespace Koshiba\Framework\Libs;

use Koshiba\Framework\Core\Registry\Component;

class Cache {
	use Component;

	/**
	 * Sets cache for Provided data
	 * for Provided time
	 * (default: 3600)
	 *
	 * @param string       $key
	 * @param array|string $data
	 * @param int          $seconds
	 *
	 * @return bool
	 */
	public function set(string $key, string|array $data, int $seconds = 3600): bool {
		$content['data']     = $data;
		$content['end_time'] = time() + $seconds;
		if (file_put_contents(self::getCacheFile($key), serialize($content))) {
			return true;
		}
		return false;
	}

	/**
	 * Gets Data from Cache,
	 * if it's `Live data`,
	 * returns it else it
	 * returns empty Array
	 *
	 * @param string $key
	 *
	 * @return array|string
	 */
	public function get(string $key): array|string {
		if (file_exists(self::getCacheFile($key))) {
			$content = file_get_contents(self::getCacheFile($key));
			$content = unserialize($content);
			if ($content['end_time'] >= time()) {
				return $content['data'];
			}

			$this->delete($key);
		}

		return "";
	}

	/**
	 * Deletes Cache and Unlinks it
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function delete(string $key): void {
		if (file_exists(self::getCacheFile($key))) {
			unlink(self::getCacheFile($key));
		}
	}

	private static function getCacheFile(string $key): string {
		return path('cache') . DS . md5($key) . '.txt';
	}
}