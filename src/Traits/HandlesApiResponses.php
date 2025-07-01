<?php

declare(strict_types=1);

namespace Ameax\AmApi\Traits;

use Ameax\AmApi\Exceptions\ApiException;

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
}
