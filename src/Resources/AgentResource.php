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

        $result = $this->extractResult($response);

        // Handle array response with agent_id field
        if (is_array($result) && isset($result['agent_id'])) {
            return (int) $result['agent_id'];
        }

        return (int) $result;
    }

    public function list(): array
    {
        $response = $this->client->get('listAgent', []);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
