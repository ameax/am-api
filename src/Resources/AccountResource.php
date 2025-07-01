<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class AccountResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addAccount', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function get(int $accountId): array
    {
        return $this->client->get('getAccount', [
            'account_id' => $accountId,
        ]);
    }

    public function update(int $accountId, array $data): bool
    {
        $response = $this->client->post('updateAccount', ['account_id' => $accountId], $data);

        return $this->isSuccess($response);
    }

    public function delete(int $accountId): bool
    {
        $response = $this->client->get('delAccount', ['account_id' => $accountId]);

        return $this->isSuccess($response);
    }

    public function login(string $login, string $password): array
    {
        return $this->client->get('executeAccountLogin', [
            'login' => $login,
            'pw' => $password,
        ]);
    }

    public function loginWithoutPassword(int $accountId): array
    {
        return $this->client->get('executeAccountLogin', [
            'account_id' => $accountId,
            'skip_password_check' => true,
        ]);
    }

    public function searchByEmail(string $email): array
    {
        return $this->client->get('searchAccount', [
            'email' => $email,
        ]);
    }

    public function changePassword(int $accountId, string $newPassword): bool
    {
        return $this->update($accountId, ['pw' => $newPassword]);
    }

    public function updateStatus(int $accountId, string $status): bool
    {
        return $this->update($accountId, ['activetype' => $status]);
    }

    public function checkEmailExists(string $email): array|false
    {
        $result = $this->searchByEmail($email);

        if (empty($result) || ! isset($result['result']) || empty($result['result'])) {
            return false;
        }

        return $result['result'];
    }

    public function list(): array
    {
        return $this->client->get('listAccounts');
    }
}
