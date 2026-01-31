<?php

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive;

/**
 * The LogLevel enum.
 *
 * Contains all the levels of logging.
 */
enum LogLevel
{
	case Error;
	case Warning;
	case Debug;
	case Info;
}
