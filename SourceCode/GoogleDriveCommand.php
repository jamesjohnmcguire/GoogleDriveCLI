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

// Examples
// GoogleDriveCommand upload videos/jp/GreenChannelWeb/2020-10-25.mp4
// GoogleDriveCommand deleteall
// GoogleDriveCommand delete 1IcurHhUiafHbqw8qfw1UEGGLrGGFzDqH

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive;

require_once "GoogleDrive.php";
require_once 'Log.php';
require_once 'LogLevel.php';

ini_set('memory_limit', '2048M');

function ParseCommandLineArguments(array $arguments): array
{
	$command = null;
	$data = null;
	$options =
	[
		'showOnlyFolders' => false,
		'showOnlyRootLevel' => false,
		'showParent' => false,
		'showShared' => false
	];

	if (!empty($arguments[1]))
	{
		$command = $arguments[1];
	}

	for ($index = 2; $index < count($arguments); $index++)
	{
		if ($arguments[$index] !== '--credentials')
		{
			continue;
		}

		$credentialsPath = $arguments[$index + 1] ?? null;

		if ($credentialsPath === null || $credentialsPath === '' ||
			strncmp($credentialsPath, '--', 2) === 0)
		{
			fwrite(STDERR, "ERROR: --credentials requires a file path.\r\n");
			exit(1);
		}

		if (!is_file($credentialsPath) || !is_readable($credentialsPath))
		{
			fwrite(STDERR, "ERROR: Credentials file is missing or unreadable: $credentialsPath\r\n");
			exit(1);
		}

		$options['serviceAccountFilePath'] = $credentialsPath;
		array_splice($arguments, $index, 2);
		$index--;
	}

	if (!empty($arguments[2]))
	{
		$exists = array_key_exists($arguments[2], $options);

		if ($exists === false)
		{
			$data = $arguments[2];
		}
	}

	foreach ($arguments as $argument)
	{
		$exists = array_key_exists($argument, $options);

		if ($exists === true)
		{
			$options[$argument] = true;
		}
	}

	return [$command, $data, $options];
}

[$command, $data, $options] = ParseCommandLineArguments($argv);
$googleDrive = new GoogleDrive($options);

echo "Command is: $command\r\n";
echo "Data is: $data\r\n";

if ($googleDrive === null)
{
	echo "ERROR: google drive object does not exist!";
}
else
{
	switch ($command)
	{
		case 'about':
			$googleDrive->About();
			break;
		case 'delete':
			$googleDrive->DeleteFile($data);
			break;
		case 'deleteall':
			$googleDrive->DeleteAllFiles();
			break;
		case 'get':
			$googleDrive->GetFile($data);
			break;
		case 'help':
			echo "Examples\r\n";
			echo "Command get <file id>\r\n";
			echo "Command list\r\n";
			echo "Command list --credentials <path>\r\n";
			echo "Command list showParent showOnlyRootLevel showOnlyFolders showShared \r\n";
			echo "Command deleteall\r\n";
			echo "Command delete 1IcurHhUiafHbqw8qfw1UEGGLrGGFzDqH\r\n";
			break;
		case 'list':
			$googleDrive->ListFiles($data);
			break;
		case 'upload':
			$googleDrive->UploadFile($data);
			break;
		default:
			break;
	}
}
