<?php

declare(strict_types=1);

namespace Ameax\AmApi\Resources;

use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Traits\HandlesApiResponses;

class ArticleResource
{
    use HandlesApiResponses;

    public function __construct(
        private readonly AmApiClient $client
    ) {}

    public function add(array $data): int
    {
        $response = $this->client->post('addArticle', [], $data);

        $this->checkForErrors($response);

        $result = $this->extractResult($response);

        // Handle array response with article_id field
        if (is_array($result) && isset($result['article_id'])) {
            return (int) $result['article_id'];
        }

        return (int) $result;
    }

    public function get(int $nodeId): array
    {
        return $this->client->get('getArticle', [
            'node_id' => $nodeId,
        ]);
    }

    public function getByNumber(string $articleNumber): array
    {
        return $this->client->get('getArticle', [
            'nr' => $articleNumber,
        ]);
    }

    public function update(string $articleNumber, array $data): bool
    {
        $response = $this->client->post('updateArticle', ['nr' => $articleNumber], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function updateByNodeId(int $nodeId, array $data): bool
    {
        $response = $this->client->post('updateArticle', ['node_id' => $nodeId], $data);

        $this->checkForErrors($response);

        return true;
    }

    public function delete(string $articleNumber): bool
    {
        $response = $this->client->get('delArticle', [
            'nr' => $articleNumber,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function deleteByNodeId(int $nodeId): bool
    {
        $response = $this->client->get('delArticle', [
            'node_id' => $nodeId,
        ]);

        $this->checkForErrors($response);

        return true;
    }

    public function listCategories(): array
    {
        $response = $this->client->get('listArticlecat', []);

        $this->checkForErrors($response);

        return (array) $this->extractResult($response);
    }
}
