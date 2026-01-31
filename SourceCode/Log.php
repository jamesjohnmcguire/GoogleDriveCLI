<?php

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

/**
 * Log class.
 *
 * Contains the functionality for log initialization.
 */
class Log
{
	/**
	 * Get Log method.
	 *
	 * @param string $name The logger name.
	 *
	 * @return object
	 */
	public static function GetLog(string $name) : object
	{
		$logFile = __DIR__ . '/Logs/' . $name . '.log';

		$logFormat =
			"[%datetime%] %level_name%: %message% %context% %extra%\n";
		$dateFormat = 'Y-m-d H:i:s T P';
		$formatter =
			new LineFormatter($logFormat, $dateFormat, false, true, true);

		$logRotate = new RotatingFileHandler(
			$logFile,
			10,
			\Monolog\Level::Debug,
			true,
			0666);
		$logRotate->setFormatter($formatter);

		$log = new Logger($name);
		$log->pushHandler($logRotate);

		return $log;
	}
}
