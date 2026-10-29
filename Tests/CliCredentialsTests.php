<?php

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CliCredentialsTests extends TestCase
{
	#[Test]
	public function missingCredentialsPathFailsBeforeConnecting(): void
	{
		[$exitCode, $error] = $this->runCli(
			['list', '--credentials']);

		self::assertSame(1, $exitCode);
		self::assertStringContainsString(
			'--credentials requires a file path', $error);
	}

	#[Test]
	public function missingCredentialsFileFailsBeforeConnecting(): void
	{
		$missingPath = __DIR__ . '/missing-service-account.json';
		[$exitCode, $error] = $this->runCli(
			['list', '--credentials', $missingPath]);

		self::assertSame(1, $exitCode);
		self::assertStringContainsString(
			'Credentials file is missing or unreadable', $error);
	}

	private function runCli(array $arguments): array
	{
		$command = array_merge(
			[PHP_BINARY, __DIR__ . '/../SourceCode/GoogleDriveCommand.php'],
			$arguments);
		$descriptors = [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ['pipe', 'w']
		];
		$process = proc_open($command, $descriptors, $pipes);

		self::assertIsResource($process);
		fclose($pipes[0]);
		$standardOutput = stream_get_contents($pipes[1]);
		$standardError = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);

		return [proc_close($process), $standardError, $standardOutput];
	}
}