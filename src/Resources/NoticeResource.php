<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class NoticeResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addNotice', [], $data);

        $this->checkForErrors($response);

        return (int) $this->extractResult($response);
    }

    public function get(int $noticeId): array
    {
        return $this->client->get('getNotice', [
            'notice_id' => $noticeId,
        ]);
    }

    public function update(int $noticeId, array $data): bool
    {
        $response = $this->client->post('updateNotice', ['notice_id' => $noticeId], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(int $noticeId): bool
    {
        $response = $this->client->post('delNotice', [], [
            'notice_id' => $noticeId,
        ]);

        $this->checkForErrors($response);

        return true;
    }
}
