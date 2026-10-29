<?php

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive\Tests;

require_once __DIR__ . '/../SourceCode/DriveRevisions.php';

use DigitalZenWorks\GoogleDrive\DriveRevisions;
use Google\Service\Drive\Resource\Revisions;
use Google\Service\Drive\Revision;
use Google\Service\Drive\RevisionList;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DriveRevisionsTests extends TestCase
{
	#[Test]
	public function listsEveryPageForTheRequestedFile(): void
	{
		$firstRevision = new Revision();
		$firstRevision->setId('revision-1');
		$secondRevision = new Revision();
		$secondRevision->setId('revision-2');

		$firstPage = new RevisionList();
		$firstPage->setRevisions([$firstRevision]);
		$firstPage->setNextPageToken('next-page');
		$lastPage = new RevisionList();
		$lastPage->setRevisions([$secondRevision]);

		$calls = [];
		$resource = $this->createMock(Revisions::class);
		$resource->expects(self::exactly(2))
			->method('listRevisions')
			->willReturnCallback(
				static function (string $fileId, array $options)
					use (&$calls, $firstPage, $lastPage): RevisionList
				{
					$calls[] = [$fileId, $options];

					return count($calls) === 1 ? $firstPage : $lastPage;
				});

		$result = (new DriveRevisions($resource))
			->ListRevisions('file-id');

		self::assertSame([$firstRevision, $secondRevision], $result);
		self::assertSame('file-id', $calls[0][0]);
		self::assertArrayNotHasKey('pageToken', $calls[0][1]);
		self::assertSame('next-page', $calls[1][1]['pageToken']);
	}

	#[Test]
	public function formatsBlobAndWorkspaceRevisionStatus(): void
	{
		$blob = new Revision();
		$blob->setId('blob-revision');
		$blob->setMimeType('application/pdf');
		$blob->setModifiedTime('2026-10-28T12:00:00Z');
		$blob->setSize('123');
		$blob->setKeepForever(true);

		$workspace = new Revision();
		$workspace->setId('workspace-revision');
		$workspace->setMimeType(
			'application/vnd.google-apps.document');

		self::assertSame(
			"blob-revision\t2026-10-28T12:00:00Z\t123\tyes\tapplication/pdf",
			DriveRevisions::FormatRevision($blob));
		self::assertStringContainsString(
			"\tn/a\tapplication/vnd.google-apps.document",
			DriveRevisions::FormatRevision($workspace));
	}
}
