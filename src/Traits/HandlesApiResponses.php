<?php

declare(strict_types=1);

namespace Ameax\AmApi\Traits;

use Ameax\AmApi\Exceptions\ApiException;
use Ameax\AmApi\Http\AmApiClient;

trait HandlesApiResponses
{
    protected function isSuccess(array $response): bool
    {
        $ack = $response['ack'] ?? $response['response']['ack'] ?? '';

        return $ack === 'ok' || $ack === 'success';
    }

    protected function extractResult(array $response): mixed
    {
        if (isset($response['result'])) {
            return $response['result'];
        }

        if (isset($response['response']['result'])) {
            return $response['response']['result'];
        }

        throw new ApiException('Missing result in API response: '.json_encode($response));
    }

    protected function checkForErrors(array $response): void
    {
        $ack = $response['ack'] ?? $response['response']['ack'] ?? null;

        if ($ack === 'error') {
            throw ApiException::fromResponse($response);
        }
    }

    /**
     * Get the last raw API response for debugging purposes
     *
     * @return array<string, mixed>|null The parsed JSON response from the last API call
     */
    protected function getLastRawResponse(): ?array
    {
        if (property_exists($this, 'client') && $this->client instanceof AmApiClient) {
            return $this->client->getLastRawResponse();
        }

        return null;
    }

    /**
     * Get the last HTTP status code for debugging purposes
     *
     * @return int|null The HTTP status code from the last API call
     */
    protected function getLastStatusCode(): ?int
    {
        if (property_exists($this, 'client') && $this->client instanceof AmApiClient) {
            return $this->client->getLastStatusCode();
        }

        return null;
    }
}
