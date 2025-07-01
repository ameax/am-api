<?php

declare(strict_types=1);

namespace Ameax\AmApi\Actions;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

abstract class AbstractAction
{
    use HandlesApiResponses;
    public function __construct(
        protected readonly AmApiClient $client
    ) {
    }

    protected function get(string $endpoint, array $params = []): array
    {
        return $this->client->get($endpoint, $params);
    }

    protected function post(string $endpoint, array $params, array $data = []): array
    {
        return $this->client->post($endpoint, $params, $data);
    }

    abstract public function execute(array $data = []): mixed;
}