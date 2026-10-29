<?php

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive\Tests;

require_once __DIR__ . '/../SourceCode/CommandLineArguments.php';

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function DigitalZenWorks\GoogleDrive\ParseCommandLineArguments;

final class CommandLineArgumentsTests extends TestCase
{
	#[Test]
	public function returnsMultiplePositionalArguments(): void
	{
		[$command, $data, $options, $positionals] =
			ParseCommandLineArguments(
				['command.php', 'revisions', 'keep', 'file-id', 'revision-id']);

		self::assertSame('revisions', $command);
		self::assertSame('keep', $data);
		self::assertSame(
			['keep', 'file-id', 'revision-id'], $positionals);
		self::assertFalse($options['showShared']);
	}

	#[Test]
	public function separatesFlagsAndCredentialsFromPositionals(): void
	{
		[$command, $data, $options, $positionals] =
			ParseCommandLineArguments(
				['command.php', 'list', 'folder-id', 'showShared',
					'--credentials', __FILE__, 'showOnlyFolders']);

		self::assertSame('list', $command);
		self::assertSame('folder-id', $data);
		self::assertSame(['folder-id'], $positionals);
		self::assertTrue($options['showShared']);
		self::assertTrue($options['showOnlyFolders']);
		self::assertSame(__FILE__, $options['serviceAccountFilePath']);
	}
}