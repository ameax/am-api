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

        $result = $this->extractResult($response);

        // Handle array response with notice_id field
        if (is_array($result) && isset($result['notice_id'])) {
            return (int) $result['notice_id'];
        }

        return (int) $result;
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
