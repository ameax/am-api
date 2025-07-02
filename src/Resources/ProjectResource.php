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

        $result = $this->extractResult($response);

        // Handle array response with projecttype_id field
        if (is_array($result) && isset($result['projecttype_id'])) {
            return (int) $result['projecttype_id'];
        }

        return (int) $result;
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
