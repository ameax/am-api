<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class ProjectResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function get(int $projectId): array
    {
        return $this->client->get('getProject', [
            'project_id' => $projectId,
        ]);
    }

    public function list(array $filters = []): array
    {
        return $this->client->get('listProject', $filters);
    }

    public function addProjectType(array $data): int
    {
        $response = $this->client->post('addProjecttype', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function updateProjectType(int $projectTypeId, array $data): bool
    {
        $response = $this->client->post('updateProjecttype', ['projecttype_id' => $projectTypeId], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function deleteProjectType(int $projectTypeId): bool
    {
        $response = $this->client->post('delProjecttype', [], [
            'projecttype_id' => $projectTypeId,
        ]);

        $this->checkForErrors($response);

        return true;
    }
}
