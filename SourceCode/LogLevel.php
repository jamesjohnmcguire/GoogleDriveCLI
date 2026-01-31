<?php

/**
 * Google Drive Command Line Interface
 *
 * A command-line tool for interacting with Google Drive.
 *
 * @package   GoogleDriveCLI
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 - 2026 by Digital Zen Works
 * @license   MIT License
 * @version   1.3.22
 * @link      https://github.com/jamesjohnmcguire/GoogleDriveCLI
 */

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
