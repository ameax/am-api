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

        return (int) $this->extractResult($response);
    }

    public function update(int $remindId, array $data): bool
    {
        $response = $this->client->post('updateRemind', ['remind_id' => $remindId], $data);

        $this->checkForErrors($response);

        return true;
    }
}
