<?php

declare(strict_types=1);

namespace Ameax\AmApi\Exceptions;

class ApiException extends \Exception
{
    private ?array $errors = null;
    private ?int $statusCode = null;
    private ?array $response = null;

    public static function fromResponse(array $response, ?int $statusCode = null): self
    {
        $errors = self::extractErrors($response);
        $message = 'API request failed';
        
        if (!empty($errors)) {
            $errorMessages = array_map(fn($error) => $error['message'] ?? 'Unknown error', $errors);
            $message .= ': ' . implode(', ', $errorMessages);
        }

        $exception = new self($message);
        $exception->errors = $errors;
        $exception->statusCode = $statusCode;
        $exception->response = $response;

        return $exception;
    }

    private static function extractErrors(array $response): array
    {
        // Check both response formats
        if (isset($response['errors'])) {
            return is_array($response['errors']) ? $response['errors'] : [];
        }

        if (isset($response['response']['errors'])) {
            return is_array($response['response']['errors']) ? $response['response']['errors'] : [];
        }

        return [];
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }
}