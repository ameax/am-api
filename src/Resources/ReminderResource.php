<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class ReminderResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addRemind', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with remind_id field
        if (is_array($result) && isset($result['remind_id'])) {
            return (int) $result['remind_id'];
        }

        return (int) $result;
    }

    public function update(int $remindId, array $data): bool
    {
        $response = $this->client->post('updateRemind', ['remind_id' => $remindId], $data);

        $this->checkForErrors($response);

        return true;
    }
}
