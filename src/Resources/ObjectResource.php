<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class ObjectResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addObject', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with object_id field
        if (is_array($result) && isset($result['object_id'])) {
            return (int) $result['object_id'];
        }

        return (int) $result;
    }

    public function get(int $objectId, int $indexId, bool $withAction = false, bool $withRemind = false): array
    {
        $params = [
            'object_id' => $objectId,
            'index_id' => $indexId,
            'with_action' => $withAction,
            'with_remind' => $withRemind,
        ];

        return $this->client->get('getObject', $params);
    }

    public function update(array $data): bool
    {
        $response = $this->client->post('updateObject', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(int $objectId, int $indexId): bool
    {
        $response = $this->client->post('delObject', [], [
            'object_id' => $objectId,
            'index_id' => $indexId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function search(array $filters = []): array
    {
        $getParams = [];
        $postParams = [];

        // Split parameters based on old API usage patterns
        if (isset($filters['object_id'])) {
            $getParams['object_id'] = $filters['object_id'];
            unset($filters['object_id']);
        }
        if (isset($filters['sco_customer_id'])) {
            $getParams['sco_customer_id'] = $filters['sco_customer_id'];
            unset($filters['sco_customer_id']);
        }

        // Remaining filters go in POST
        $postParams = $filters;

        $response = $this->client->post('searchObject', $getParams, $postParams);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }

    public function addWithCustomerObject(array $data): array
    {
        $response = $this->client->post('addObjectAndCustomerobject', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Return the full result array to get all IDs
        if (is_array($result)) {
            return $result;
        }

        // Fallback for unexpected response format
        return [
            'id' => (int) $result,
            'index_id' => 0,
            'customer_object_id' => 0,
        ];
    }

    public function updateWithCustomerObject(array $data): bool
    {
        $response = $this->client->post('updateObjectAndCustomerobject', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function addCustomerObject(int $objectId, int $indexId, int $customerId, ?int $projectId = null): int
    {
        $data = [
            'object_id' => $objectId,
            'index_id' => $indexId,
            'customer_id' => $customerId,
        ];

        if ($projectId !== null) {
            $data['project_id'] = $projectId;
        }

        $response = $this->client->post('addCustomerobject', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with customer_object_id field
        if (is_array($result) && isset($result['customer_object_id'])) {
            return (int) $result['customer_object_id'];
        }

        return (int) $result;
    }

    public function getCustomerObject(int $objectId, int $customerObjectId, bool $withAction = false, bool $withRemind = false): array
    {
        $data = [
            'object_id' => $objectId,
            'customer_object_id' => $customerObjectId,
            'with_action' => $withAction,
            'with_remind' => $withRemind,
        ];

        return $this->client->post('getCustomerobject', [], $data);
    }

    public function updateCustomerObject(array $data): bool
    {
        $response = $this->client->post('updateCustomerobject', [], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function deleteCustomerObject(int $objectId, int $customerObjectId): bool
    {
        $response = $this->client->post('delCustomerobject', [], [
            'object_id' => $objectId,
            'customer_object_id' => $customerObjectId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function searchCustomerObjects(array $filters = []): array
    {
        $response = $this->client->post('searchCustomerobject', [], $filters);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
