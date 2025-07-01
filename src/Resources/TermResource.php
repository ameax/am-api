<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class TermResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addTerm', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function get(int $termId): array
    {
        return $this->client->get('getTerm', [
            'term_id' => $termId,
        ]);
    }

    public function update(int $termId, array $data): bool
    {
        $data['term_id'] = $termId;
        $response = $this->client->post('updateTerm', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(int $termId): bool
    {
        $response = $this->client->get('delTerm', [
            'term_id' => $termId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function list(array $filters = []): array
    {
        $response = $this->client->get('listTerm', $filters);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }

    public function search(array $filters = []): array
    {
        $response = $this->client->get('searchTerm', $filters);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
