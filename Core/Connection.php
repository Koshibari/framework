<?php

namespace Koshiba\Framework\Core;

use Koshiba\Framework\Core\Abstracts\Singletone;
use Koshiba\Framework\Core\Config\Config;
use Koshiba\Framework\Libs\Static\Helper;
use PDO;
use R;


class Connection {
	use Singletone;

	/**
	 * PDO Entity
	 * @var PDO
	 */
	protected PDO $link;

	/**
	 * Number of executed Queries
	 * @var int
	 */
	public static int $countSql = 0;

	/**
	 * Array of executed Queries
	 * @var array
	 */
	public static array $queries = [];

	/**
	 * Connection configs
	 * @var Config
	 */
	protected static Config $config;

	protected function __construct() {
		self::$config = Helper::dbConfig();
		$conConfig    = self::$config->connection;
		require LIBS . DS . 'rb.php';
		R::setup($conConfig['dsn'], $conConfig['user'], $conConfig['password']);
		R::freeze(true);
	}


}