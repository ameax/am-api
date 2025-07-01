<?php

declare(strict_types=1);

namespace Ameax\AmApi\Config;

final class Config
{
    public function __construct(
        public readonly string $apiUrl,
        public readonly string $username,
        public readonly string $password,
        public readonly bool $debug = false,
        public readonly array $httpOptions = [],
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->apiUrl)) {
            throw new \InvalidArgumentException('API URL cannot be empty');
        }

        if (empty($this->username) || empty($this->password)) {
            throw new \InvalidArgumentException('Username and password are required');
        }

        if (!filter_var($this->apiUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid API URL format');
        }
    }

    public function getAuth(): array
    {
        return [$this->username, $this->password];
    }
}