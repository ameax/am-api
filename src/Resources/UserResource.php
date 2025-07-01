<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;

class UserResource
{
    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function list(): array
    {
        return $this->client->get('listUser');
    }
}
