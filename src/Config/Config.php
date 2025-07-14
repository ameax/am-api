<?php

declare(strict_types=1);

namespace Ameax\AmApi\Config;

final class Config
{
    private const AUTH_TYPE_BASIC = 'basic';

    private const AUTH_TYPE_TOKEN = 'token';

    private function __construct(
        public readonly string $apiUrl,
        public readonly ?string $username,
        public readonly ?string $password,
        public readonly ?string $apiToken,
        private readonly string $authType,
        public readonly bool $debug = false,
        public readonly array $httpOptions = [],
    ) {
        $this->validate();
    }

    public static function withBasicAuth(
        string $apiUrl,
        string $username,
        string $password,
        bool $debug = false,
        array $httpOptions = []
    ): self {
        return new self(
            apiUrl: $apiUrl,
            username: $username,
            password: $password,
            apiToken: null,
            authType: self::AUTH_TYPE_BASIC,
            debug: $debug,
            httpOptions: $httpOptions
        );
    }

    public static function withApiToken(
        string $apiUrl,
        string $apiToken,
        bool $debug = false,
        array $httpOptions = []
    ): self {
        return new self(
            apiUrl: $apiUrl,
            username: null,
            password: null,
            apiToken: $apiToken,
            authType: self::AUTH_TYPE_TOKEN,
            debug: $debug,
            httpOptions: $httpOptions
        );
    }

    public static function fromArray(array $data): self
    {
        $authType = $data['authType'] ?? self::AUTH_TYPE_BASIC;

        if ($authType === self::AUTH_TYPE_TOKEN) {
            return self::withApiToken(
                apiUrl: $data['apiUrl'],
                apiToken: $data['apiToken'],
                debug: $data['debug'] ?? false,
                httpOptions: $data['httpOptions'] ?? []
            );
        }

        return self::withBasicAuth(
            apiUrl: $data['apiUrl'],
            username: $data['username'],
            password: $data['password'],
            debug: $data['debug'] ?? false,
            httpOptions: $data['httpOptions'] ?? []
        );
    }

    private function validate(): void
    {
        if (empty($this->apiUrl)) {
            throw new \InvalidArgumentException('API URL cannot be empty');
        }

        if (! filter_var($this->apiUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid API URL format');
        }

        if ($this->authType === self::AUTH_TYPE_TOKEN && empty($this->apiToken)) {
            throw new \InvalidArgumentException('API token cannot be empty');
        }

        if ($this->authType === self::AUTH_TYPE_BASIC) {
            if (empty($this->username) || empty($this->password)) {
                throw new \InvalidArgumentException('Username and password are required for basic auth');
            }
        }
    }

    public function isTokenAuth(): bool
    {
        return $this->authType === self::AUTH_TYPE_TOKEN;
    }

    public function isBasicAuth(): bool
    {
        return $this->authType === self::AUTH_TYPE_BASIC;
    }

    public function getAuth(): ?array
    {
        if ($this->isBasicAuth()) {
            return [$this->username, $this->password];
        }

        return null;
    }

    public function getAuthHeaders(): array
    {
        if ($this->isTokenAuth()) {
            return [
                'Authorization' => 'Bearer '.$this->apiToken,
            ];
        }

        return [];
    }

    public function toArray(): array
    {
        $data = [
            'apiUrl' => $this->apiUrl,
            'authType' => $this->authType,
            'debug' => $this->debug,
            'httpOptions' => $this->httpOptions,
        ];

        if ($this->isBasicAuth()) {
            $data['username'] = $this->username;
            $data['password'] = $this->password;
        } else {
            $data['apiToken'] = $this->apiToken;
        }

        return $data;
    }
}
