<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class PurposeResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function get(int $purposeId): array
    {
        return $this->client->get('getPurpose', [
            'purpose_id' => $purposeId,
        ]);
    }

    public function list(): array
    {
        $response = $this->client->get('listPurpose', []);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
