<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class PersonResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addPerson', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function get(int $personId): array
    {
        return $this->client->get('getPerson', [
            'person_id' => $personId,
        ]);
    }

    public function update(int $personId, array $data): bool
    {
        $response = $this->client->post('updatePerson', ['person_id' => $personId], $data);

        return $this->isSuccess($response);
    }

    public function delete(int $personId): bool
    {
        $response = $this->client->get('delPerson', ['person_id' => $personId]);

        return $this->isSuccess($response);
    }
}
