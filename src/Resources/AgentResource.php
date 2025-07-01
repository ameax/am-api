<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class AgentResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addAgent', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function list(): array
    {
        $response = $this->client->get('listAgent', []);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
