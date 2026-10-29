<?php

declare(strict_types=1);

namespace DigitalZenWorks\GoogleDrive;

use Google\Service\Drive\Resource\Revisions;
use Google\Service\Drive\Revision;

final class DriveRevisions
{
	private Revisions $resource;

	public function __construct(Revisions $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @return Revision[]
	 */
	public function ListRevisions(string $fileId): array
	{
		$revisions = [];
		$pageToken = null;

		do
		{
			$options = [
				'fields' =>
					'nextPageToken,revisions(id,keepForever,mimeType,' .
					'modifiedTime,size)',
				'pageSize' => 1000
			];

			if ($pageToken !== null)
			{
				$options['pageToken'] = $pageToken;
			}

			$page = $this->resource->listRevisions($fileId, $options);

			foreach ($page->getRevisions() ?? [] as $revision)
			{
				$revisions[] = $revision;
			}

			$pageToken = $page->getNextPageToken();
		}
		while ($pageToken !== null && $pageToken !== '');

		return $revisions;
	}

	public function KeepRevisionForever(
		string $fileId, string $revisionId): Revision
	{
		$fields = ['fields' => 'id,keepForever,mimeType'];
		$current = $this->resource->get(
			$fileId, $revisionId, $fields);

		if (strpos(
			(string)$current->getMimeType(),
			'application/vnd.google-apps.') === 0)
		{
			throw new \InvalidArgumentException(
				'Keep forever only applies to binary file revisions.');
		}

		if ($current->getKeepForever() === true)
		{
			return $current;
		}

		$update = new Revision();
		$update->setKeepForever(true);
		$this->resource->update(
			$fileId, $revisionId, $update,
			['fields' => 'id,keepForever']);

		$confirmed = $this->resource->get(
			$fileId, $revisionId, $fields);

		if ($confirmed->getKeepForever() !== true)
		{
			throw new \RuntimeException(
				'Drive did not confirm keepForever for this revision.');
		}

		return $confirmed;
	}

	public static function FormatRevision(Revision $revision): string
	{
		$mimeType = $revision->getMimeType();
		$isWorkspaceFile = strpos(
			(string)$mimeType, 'application/vnd.google-apps.') === 0;
		$kept = $isWorkspaceFile
			? 'n/a'
			: ($revision->getKeepForever() === true ? 'yes' : 'no');

		return implode("\t", [
			$revision->getId(),
			$revision->getModifiedTime() ?? '-',
			$revision->getSize() ?? '-',
			$kept,
			$mimeType ?? '-'
		]);
	}
}
