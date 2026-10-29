<?php

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive;

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

	$positionals = [];

	foreach (array_slice($arguments, 2) as $argument)
	{
		if (!array_key_exists($argument, $options))
		{
			$positionals[] = $argument;
		}
	}

	return [$command, $data, $options, $positionals];
}