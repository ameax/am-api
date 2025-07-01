<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class ActionResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addAction', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function get(string $dateFrom, string $dateTill): array
    {
        return $this->client->get('getAction', [
            'add_dt_from' => $dateFrom,
            'add_dt_till' => $dateTill,
        ]);
    }

    public function getById(int $actionId): array
    {
        return $this->client->get('getAction', [
            'action_id' => $actionId,
        ]);
    }

    public function update(int $actionId, array $data): bool
    {
        $response = $this->client->post('updateAction', ['action_id' => $actionId], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function addFile(int $actionId, string $filePath, string $mimeType, string $fileName): int
    {
        $response = $this->client->post('addActionFile', [], [
            'action_id' => $actionId,
            'file' => [
                'path' => $filePath,
                'mime' => $mimeType,
                'name' => $fileName,
            ],
        ]);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function deleteFile(int $fileId): bool
    {
        $response = $this->client->get('delActionFile', [
            'file_id' => $fileId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function listFiles(int $actionId): array
    {
        $response = $this->client->get('listActionFile', [
            'action_id' => $actionId,
        ]);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }

    public function loadFile(int $actionId, int $fileId): string
    {
        return $this->client->get('loadActionFile', [
            'action_id' => $actionId,
            'file_id' => $fileId,
        ]);
    }
}
