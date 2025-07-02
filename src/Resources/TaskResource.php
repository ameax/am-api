<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class TaskResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addTask', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with task_id field
        if (is_array($result) && isset($result['task_id'])) {
            return (int) $result['task_id'];
        }

        return (int) $result;
    }

    public function get(int $taskId): array
    {
        return $this->client->post('getTask', [], [
            'task_id' => $taskId,
        ]);
    }

    public function update(int $taskId, array $data): bool
    {
        $data['task_id'] = $taskId;
        $response = $this->client->post('updateTask', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function executeProceeding(int $taskId, int $proceedingId): bool
    {
        $response = $this->client->post('executeTaskProceeding', [], [
            'task_id' => $taskId,
            'proceeding_id' => $proceedingId,
        ]);

        $this->checkForErrors($response);

        return true;
    }
}
