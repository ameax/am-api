<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class FileResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function upload(string $module, int $moduleId, string $field, string $filePath, string $mimeType, string $fileName): int
    {
        $response = $this->client->post('uploadFile', [], [
            'mod' => $module,
            'mod_id' => $moduleId,
            'field' => $field,
            'file' => [
                'path' => $filePath,
                'mime' => $mimeType,
                'name' => $fileName,
            ],
        ]);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function uploadToCustomer(int $customerId, string $field, string $filePath, string $mimeType, string $fileName): int
    {
        return $this->upload('customer', $customerId, $field, $filePath, $mimeType, $fileName);
    }

    public function uploadToPerson(int $personId, string $field, string $filePath, string $mimeType, string $fileName): int
    {
        return $this->upload('person', $personId, $field, $filePath, $mimeType, $fileName);
    }

    public function uploadToTask(int $taskId, string $filePath, string $mimeType, string $fileName): int
    {
        return $this->upload('task', $taskId, 'file', $filePath, $mimeType, $fileName);
    }

    public function list(string $module, int $moduleId): array
    {
        $response = $this->client->get('getFileList', [
            'mod' => $module,
            'mod_id' => $moduleId,
        ]);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }

    public function listCustomerFiles(int $customerId): array
    {
        return $this->list('customer', $customerId);
    }

    public function listPersonFiles(int $personId): array
    {
        return $this->list('person', $personId);
    }

    public function listTaskFiles(int $taskId): array
    {
        return $this->list('task', $taskId);
    }
}
