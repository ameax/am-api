<?php

declare(strict_types=1);

namespace Ameax\AmApi\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Ameax\AmApi\Config\Config;
use Ameax\AmApi\Exceptions\ApiException;
use Psr\Http\Message\ResponseInterface;

final class AmApiClient
{
    private GuzzleClient $httpClient;
    private ?array $lastRawResponse = null;
    private ?int $lastStatusCode = null;

    public function __construct(
        private readonly Config $config,
        ?GuzzleClient $httpClient = null
    ) {
        $this->httpClient = $httpClient ?? $this->createDefaultClient();
    }

    private function createDefaultClient(): GuzzleClient
    {
        $options = array_merge([
            'base_uri' => $this->config->apiUrl,
            'auth' => $this->config->getAuth(),
            'headers' => [
                'Accept' => 'application/json',
            ],
            'debug' => $this->config->debug,
            'timeout' => 30,
            'connect_timeout' => 10,
        ], $this->config->httpOptions);

        return new GuzzleClient($options);
    }

    public function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $options = [];

            if (!empty($data) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
                $options['form_params'] = $data;
            }

            $response = $this->httpClient->request($method, $endpoint, $options);

            return $this->parseResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiException(
                'HTTP request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function get(string $endpoint, array $params = []): array
    {
        $url = $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $this->request('GET', $url);
    }

    public function post(string $endpoint, array $params, array $data = []): array
    {
        $url = $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $this->request('POST', $url, $data);
    }

    private function parseResponse(ResponseInterface $response): array
    {
        $this->lastStatusCode = $response->getStatusCode();

        $bodyContent = $response->getBody()->getContents();
        
        if (empty($bodyContent)) {
            $this->lastRawResponse = [];
            return [];
        }

        $result = json_decode($bodyContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                'Invalid JSON response: ' . json_last_error_msg()
            );
        }

        $this->lastRawResponse = $result;

        return $result;
    }

    public function getLastRawResponse(): ?array
    {
        return $this->lastRawResponse;
    }

    public function getLastStatusCode(): ?int
    {
        return $this->lastStatusCode;
    }
}