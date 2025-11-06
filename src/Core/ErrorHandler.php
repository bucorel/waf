<?php
namespace Bucorel\Waf\Core;

class ErrorHandler{

	protected static string $mode = 'prod';
	protected static string $logFile = '/tmp/app.log';

	public static function register(array $config = []){
		self::$mode = $config['APP_MODE'] ?? 'prod';
		self::$logFile = $config['LOG_FILE'] ?? '/tmp/app.log';

		set_error_handler([self::class, 'handleError']);
		set_exception_handler([self::class, 'handleException']);
		register_shutdown_function([self::class, 'handleShutdown']);
	}

	public static function handleError($errno, $errstr, $errfile, $errline){
		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

	public static function handleException($e){
		$message = 'Internal Server Error';
		$details = [];

		if (self::$mode === 'debug') {
			$message = $e->getMessage();
			$details = [
				'type' => get_class($e),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => explode("\n", $e->getTraceAsString()),
			];
		} else {
			self::logException($e);
		}

		JsonResponse::showFatalError($message, $details);
	}

	protected static function logException($e){
		$entry = sprintf(
			"[%s] %s in %s:%d\nStack trace:\n%s\n\n",
			date('Y-m-d H:i:s'),
			$e->getMessage(),
			$e->getFile(),
			$e->getLine(),
			$e->getTraceAsString()
		);
		error_log($entry);
	}

	public static function handleShutdown(){
		$error = error_get_last();
		if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR))) {
			$msg = 'Fatal Error';
			$details = [];

			if (self::$mode === 'debug') {
				$msg = $error['message'];
				$details = [
					'file' => $error['file'],
					'line' => $error['line'],
					'type' => $error['type'],
				];
			} else {
				$entry = sprintf("[%s] FATAL: %s in %s:%d\n", date('Y-m-d H:i:s'), $error['message'], $error['file'], $error['line']);
				error_log($entry);
			}

			JsonResponse::showFatalError($msg, $details);
		}
	}
}

