<?php

declare(strict_types=1);

if (PHP_SAPI == 'cli')
{
	defined('EOL') OR define('EOL', PHP_EOL);
}
else
{
	defined('EOL') OR define('EOL', '<br />'.PHP_EOL);
}

class Common
{
	/**
	 * Checks if the current content is different from the previous content
	 *
	 * @param mixed $previous Reference to the previous content (will be updated)
	 * @param mixed $current The current content to compare
	 * @return bool True if contents are different, false if same
	 */
	public static function AreContentsSame(mixed &$previous, mixed $current): bool
	{
		$different = false;

		if (empty($current))
		{
			// regardless of state of previous, if current is empty,
			// it's point of action
			$different = true;
			$previous = $current;
		}
		else
		{
			if (empty($previous))
			{
				$previous = $current;
				$different = true;
			}
			else if ($previous !== $current)
			{
				$previous = $current;
				$different = true;
			}
		}

		return $different;
	}

	/**
	 * Converts a Japanese date string to ISO format (Y-m-d)
	 *
	 * @param string $date Japanese date string (e.g., "2020年10月25日")
	 * @return string ISO formatted date string (Y-m-d)
	 */
	public static function ConvertJapaneseDate(string $date): string
	{
		$date = str_replace('年', '-', $date);
		$date = str_replace('月', '-', $date);
		$date = str_replace('日', '', $date);

		$time = strtotime($date);
		$date = date('Y-m-d', $time);

		return $date;
	}

	/**
	 * Flushes output buffers
	 *
	 * @return void
	 */
	public static function FlushBuffers(): void
	{
		if (ob_get_level() > 0)
		{
			ob_flush();
		}
		flush();
	}

	/**
	 * Gets the current time with microseconds in Asia/Tokyo timezone
	 *
	 * @return string Formatted time string (Y-m-d H:i:s:u) or 'none' on failure
	 */
	public static function GetNowMicroTime(): string
	{
		$time = 'none';
		$timezone = new DateTimeZone('Asia/Tokyo');
		$now = DateTime::createFromFormat('U.u', microtime(true));

		if (!empty($now))
		{
			$now->setTimeZone($timezone);
			$time = $now->format("Y-m-d H:i:s:u");
		}


		return $time;
	}

	/**
	 * Gets the current time with microseconds in Asia/Tokyo timezone
	 *
	 * @return string Formatted time string (Y-m-d H:i:s:u) or empty string on failure
	 */
	public static function GetNowTime(): string
	{
		$timezone = new DateTimeZone('Asia/Tokyo');
		$time = '';
		$now = new DateTime();
		if (!empty($now))
		{
			$now->setTimeZone($timezone);
			$time = $now->format("Y-m-d H:i:s:u");
		}

		return $time;
	}

	/**
	 * Validates a time string and optionally checks if it's within a time range
	 *
	 * @param string $time The time string to validate (HH:MM format)
	 * @param string|null $beginTime Optional start time for range check (HH:MM format)
	 * @param string|null $endTime Optional end time for range check (HH:MM format)
	 * @return bool True if time is valid (and within range if specified), false otherwise
	 */
	public static function IsValidTime(string $time, ?string $beginTime = null,
		?string $endTime = null): bool
	{
		$isTime = false;
		$time = trim($time);

		$length = strlen($time);
		if ($length < 6)
		{
			$test = preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $time);
			if ($test == 1)
			{
				if (($beginTime != null) && ($endTime != null))
				{
					$testTime = DateTime::createFromFormat('H:i', $time);
					$begin = DateTime::createFromFormat('H:i', $beginTime);
					$end = DateTime::createFromFormat('H:i', $endTime);
					if ($testTime > $begin && $testTime < $end)
					{
						$isTime = true;
					}
				}
				else
				{
					$isTime = true;
				}
			}
		}

		return $isTime;
	}

	/**
	 * Configures PHP settings for debug mode
	 *
	 * @return void
	 */
	public static function SetDebugMode(): void
	{
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
		ini_set('implicit_flush', 1);
		ob_implicit_flush(true);
		set_time_limit(0);

		ini_set('xdebug.var_display_max_depth', -1);
		ini_set('xdebug.var_display_max_children', -1);
		ini_set('xdebug.var_display_max_data', -1);
	}
}
