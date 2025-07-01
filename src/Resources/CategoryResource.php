<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class CategoryResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addCat', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function update(int $categoryId, array $data): bool
    {
        $response = $this->client->post('updateCat', ['cat_id' => $categoryId], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(int $categoryId): bool
    {
        $response = $this->client->post('delCat', [], [
            'cat_id' => $categoryId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function list(): array
    {
        $response = $this->client->get('listCat', []);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
