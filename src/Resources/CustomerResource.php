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
    ) {
    }

    public function add(array $data): int
    {
        $response = $this->client->post('addCustomer', [], $data);
        
        $this->checkForErrors($response);
        return (int) $this->extractResult($response);
    }

    public function get(int $customerId): array
    {
        return $this->client->get('getCustomer', [
            'customer_id' => $customerId
        ]);
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
}