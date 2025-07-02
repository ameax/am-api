<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class SaleResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addSale', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with sale_id field
        if (is_array($result) && isset($result['sale_id'])) {
            return (int) $result['sale_id'];
        }

        return (int) $result;
    }

    public function get(int $saleId, bool $includeFiles = false, bool $includeImages = false, bool $includeAudios = false, bool $includeVideos = false): array
    {
        $data = [
            'sale_id' => $saleId,
        ];

        if ($includeFiles) {
            $data['files'] = 1;
        }
        if ($includeImages) {
            $data['images'] = 1;
        }
        if ($includeAudios) {
            $data['audios'] = 1;
        }
        if ($includeVideos) {
            $data['videos'] = 1;
        }

        return $this->client->post('getSale', [], $data);
    }

    public function update(int $saleId, array $data): bool
    {
        $data['sale_id'] = $saleId;
        $response = $this->client->post('updateSale', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(int $saleId): bool
    {
        $response = $this->client->post('delSale', [], [
            'sale_id' => $saleId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function list(int $customerId): array
    {
        $response = $this->client->post('listSale', [], [
            'customer_id' => $customerId,
        ]);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }

    public function search(array $filters = []): array
    {
        $getParams = [];
        $postParams = [];

        // Handle custom field search parameters (ss_* prefix)
        foreach ($filters as $key => $value) {
            if (strpos($key, 'ss_') === 0) {
                $getParams[$key] = $value;
                unset($filters[$key]);
            }
        }

        // Remaining filters go in POST
        $postParams = $filters;

        $response = $this->client->post('searchSale', $getParams, $postParams);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
