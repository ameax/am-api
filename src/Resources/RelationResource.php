<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class RelationResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {
    }

    public function add(int $sourceCustomerId, int $targetCustomerId, string $relation): int
    {
        $data = [
            'source_customer_id' => $sourceCustomerId,
            'target_customer_id' => $targetCustomerId,
            'relation' => $relation,
        ];

        $response = $this->client->post('addCustomerRelation', [], $data);
        
        $this->checkForErrors($response);
        return (int) $this->extractResult($response);
    }

    public function get(int $customerId, ?string $relation = null): array
    {
        $params = [
            'customer_id' => $customerId,
        ];

        if ($relation !== null) {
            $params['relation'] = $relation;
        }

        return $this->client->get('getCustomerRelation', $params);
    }

    public function delete(int $relationId): bool
    {
        $response = $this->client->get('delCustomerRelation', ['relation_id' => $relationId]);
        return $this->isSuccess($response);
    }
}