<?php

namespace Koshiba\Framework\Core\Abstracts;

trait Singletone {

	/**
	 * Instance of @Singletone
	 * @var self
	 */
	protected static self $instance;

	/**
	 * Creates instance of  or returns it if it's already created
	 *
	 * @return self
	 */
	public static function instance(): self {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}