<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class CommissionResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addCommission', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function get(array $params = []): array
    {
        return $this->client->get('getCommission', $params);
    }

    public function update(int $commissionId, array $data): bool
    {
        $response = $this->client->post('updateCommission', ['id' => $commissionId], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(int $commissionId): bool
    {
        $response = $this->client->post('delCommission', [], [
            'id' => $commissionId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function list(array $filters = []): array
    {
        $response = $this->client->get('listCommissions', $filters);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }

    public function getByCustomer(int $customerId): array
    {
        return $this->get(['customer_id' => $customerId]);
    }

    public function getByUser(int $userId): array
    {
        return $this->get(['user_id' => $userId]);
    }

    public function getByFactorRange(float $factorFrom, float $factorTill): array
    {
        return $this->get([
            'factor_from' => $factorFrom,
            'factor_till' => $factorTill,
        ]);
    }
}
