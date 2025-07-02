<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class CustomerResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addCustomer', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with customer_id field
        if (is_array($result) && isset($result['customer_id'])) {
            return (int) $result['customer_id'];
        }

        return (int) $result;
    }

    public function get(int $customerId, array $includes = []): array
    {
        $params = ['customer_id' => $customerId];

        // Add optional includes
        $availableIncludes = ['files', 'images', 'faktura', 'cat', 'commission', 'person', 'project', 'relation'];
        foreach ($availableIncludes as $include) {
            if (in_array($include, $includes)) {
                $params[$include] = 1;
            }
        }

        return $this->client->get('getCustomer', $params);
    }

    public function update(int $customerId, array $data): bool
    {
        $response = $this->client->post('updateCustomer', ['customer_id' => $customerId], $data);

        return $this->isSuccess($response);
    }

    public function delete(int $customerId): bool
    {
        $response = $this->client->get('delCustomer', ['customer_id' => $customerId]);

        return $this->isSuccess($response);
    }

    public function search(array $filters): array
    {
        return $this->client->get('searchCustomer', $filters);
    }

    public function load(array $filters): array
    {
        return $this->client->get('loadCustomer', $filters);
    }

    /**
     * Store shipping address data for a customer
     * Note: This method expects data in API format (already mapped)
     */
    public function storeShippingAddress(int $customerId, array $addressData): int|false
    {
        try {
            $addressName = $addressData['name1'] ?? 'Delivery Address';

            $deliveryAddressCustomerId = $this->add($addressData);

            $shippingAddressJson = json_encode([
                'name' => $addressData['name1'] ?? null,
                'street' => $addressData['strasse'] ?? null,
                'postal_code' => $addressData['plz'] ?? null,
                'city' => $addressData['ort'] ?? null,
                'country' => $addressData['isoland'] ?? 'DE',
                'type' => 'delivery',
            ]);

            $updateData = [
                'shipping_address_data' => $shippingAddressJson,
            ];

            $updateResult = $this->update($deliveryAddressCustomerId, $updateData);

            if ($updateResult) {
                // This would need RelationResource to be injected or accessed
                // $relationId = $this->relations->add($customerId, $deliveryAddressCustomerId, 'deliveryaddress');
                return $deliveryAddressCustomerId;
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function getHistory(array $params = []): array
    {
        return $this->client->get('getCustomerHistory', $params);
    }

    public function addCategory(int $customerId, int $categoryId): bool
    {
        $response = $this->client->post('addCustomerCat', [], [
            'customer_id' => $customerId,
            'cat_id' => $categoryId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function removeCategory(int $customerId, int $categoryId): bool
    {
        $response = $this->client->post('delCustomerCat', [], [
            'customer_id' => $customerId,
            'cat_id' => $categoryId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function addProject(int $customerId, array $projectData): int
    {
        $projectData['customer_id'] = $customerId;
        $response = $this->client->post('addCustomerProject', [], $projectData);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with project_id field
        if (is_array($result) && isset($result['project_id'])) {
            return (int) $result['project_id'];
        }

        return (int) $result;
    }

    public function updateProject(int $projectId, array $projectData): bool
    {
        $response = $this->client->post('updateCustomerProject', ['project_id' => $projectId], $projectData);

        $this->checkForErrors($response);

        return true;
    }

    public function deleteProject(int $projectId): bool
    {
        $response = $this->client->post('delCustomerProject', [], [
            'project_id' => $projectId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function addRelation(int $customerId, int $relatedCustomerId, string $relationType = ''): int
    {
        $response = $this->client->post('addCustomerRelation', [], [
            'customer_id' => $customerId,
            'customer2_id' => $relatedCustomerId,
            'type' => $relationType,
        ]);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with relation_id field
        if (is_array($result) && isset($result['relation_id'])) {
            return (int) $result['relation_id'];
        }

        return (int) $result;
    }

    public function getRelations(int $customerId): array
    {
        return $this->client->get('getCustomerRelation', [
            'customer_id' => $customerId,
        ]);
    }

    public function deleteRelation(int $relationId): bool
    {
        $response = $this->client->post('delCustomerRelation', [], [
            'relation_id' => $relationId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function setReceiptDefaults(int $customerId, array $defaults): bool
    {
        $defaults['customer_id'] = $customerId;
        $response = $this->client->post('addCustomerReceipt', [], $defaults);

        $this->checkForErrors($response);

        return true;
    }

    public function updateReceiptDefaults(int $customerId, array $defaults): bool
    {
        $defaults['customer_id'] = $customerId;
        $response = $this->client->post('updateCustomerReceipt', [], $defaults);

        $this->checkForErrors($response);

        return true;
    }

    public function executeProceeding(int $customerId, int $proceedingId): bool
    {
        $response = $this->client->post('executeCustomerProceeding', [], [
            'customer_id' => $customerId,
            'proceeding_id' => $proceedingId,
        ]);

        $this->checkForErrors($response);

        return true;
    }
}
