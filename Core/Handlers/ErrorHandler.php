<?php

namespace Koshiba\Framework\Core\Handlers;

use Koshiba;
use Throwable;

class ErrorHandler {
	public function __construct() {
		if (Koshiba::DEBUG->bool()) {
			error_reporting(-1);
		} else {
			error_reporting(0);
		}
		set_error_handler([
			$this,
			'handle'
		]);

		ob_start();
		register_shutdown_function([
			$this,
			'handleFatalError'
		]);

		set_exception_handler([
			$this,
			'handleException'
		]);
	}

	public function handle($errno, $errstr, $errfile, $errline): bool {
		$this->log("", $errstr, $errfile, $errline);
		if (Koshiba::DEBUG->bool() || $errno & (E_USER_ERROR | E_RECOVERABLE_ERROR)) {
			$this->displayError($errno, $errstr, $errfile, $errline);
		}
		return true;
	}

	public function handleFatalError(): void {
		$error = error_get_last();
		if (!empty($error) && $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
			ob_end_clean();
			$this->log("[SHUTDOWN]", $error['message'], $error['file'], $error['line']);
			$this->displayError($error['type'], $error['message'], $error['file'], $error['line']);
		} else {
			ob_end_flush();
		}
	}

	public function handleException(Throwable $e): void {
		$this->log("Exception", $e->getMessage(), $e->getFile(), $e->getLine());
		$this->displayError('Thrown Exception', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
	}

	protected function log($type, $errstr, $errfile, $errline): void {
		error_log("Error $type=> Message: $errstr | File: $errfile | Line: $errline");
		$d = date('Y-m-d H:i:s');
		error_log("[$d] Error $type=> Message: $errstr | File: $errfile | Line: $errline\n!=============================================!\n", 3, path('log') . DS . Koshiba::ERROR_HANDLER_LOG->value);
	}

	protected function displayError($errno, $errstr, $errfile, $errline, $response = 500): void {
		http_response_code($response);
		if ($response === 404 && !Koshiba::DEBUG->bool()) {
			require WEB . '/errors/404.html';
			die;
		}
		if (Koshiba::DEBUG->bool()) {
			require WEB . '/errors/dev.php';
		} else {
			require WEB . '/errors/prod.php';
		}
		die;
	}
}