<?php

declare(strict_types=1);

namespace Ameax\AmApi\Services;

use Ameax\AmApi\Exceptions\ApiException;

class ResponseParser
{
    public static function isSuccess(array $response): bool
    {
        $ack = $response['ack'] ?? $response['response']['ack'] ?? '';

        return $ack === 'ok' || $ack === 'success';
    }

    public static function extractResult(array $response): mixed
    {
        if (isset($response['result'])) {
            return $response['result'];
        }

        if (isset($response['response']['result'])) {
            return $response['response']['result'];
        }

        throw new ApiException('Missing result in API response: ' . json_encode($response));
    }

    public static function checkForErrors(array $response): void
    {
        $ack = $response['ack'] ?? $response['response']['ack'] ?? null;

        if ($ack === 'error') {
            throw ApiException::fromResponse($response);
        }
    }
}