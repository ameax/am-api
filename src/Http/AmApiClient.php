<?php

declare(strict_types=1);

namespace Ameax\AmApi\Http;

use Ameax\AmApi\Config\Config;
use Ameax\AmApi\Exceptions\ApiException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
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
        $headers = [
            'Accept' => 'application/json',
        ];

        // Add token auth headers if using API token
        $headers = array_merge($headers, $this->config->getAuthHeaders());

        $options = array_merge([
            'base_uri' => $this->config->apiUrl,
            'headers' => $headers,
            'debug' => $this->config->debug,
            'timeout' => 30,
            'connect_timeout' => 10,
        ], $this->config->httpOptions);

        // Only add basic auth if not using token
        if ($this->config->isBasicAuth()) {
            $options['auth'] = $this->config->getAuth();
        }

        return new GuzzleClient($options);
    }

    public function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $options = [];

            if (! empty($data) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
                // Check if we have file uploads
                $hasFiles = false;
                foreach ($data as $value) {
                    if ($value instanceof \CURLFile) {
                        $hasFiles = true;
                        break;
                    }
                }

                if ($hasFiles) {
                    // Use multipart for file uploads
                    $multipart = [];
                    foreach ($data as $key => $value) {
                        if ($value instanceof \CURLFile) {
                            $multipart[] = [
                                'name' => $key,
                                'contents' => fopen($value->getFilename(), 'r'),
                                'filename' => $value->getPostFilename() ?: basename($value->getFilename()),
                            ];
                        } else {
                            $multipart[] = [
                                'name' => $key,
                                'contents' => $value,
                            ];
                        }
                    }
                    $options['multipart'] = $multipart;
                } else {
                    // Use form_params for regular data
                    $options['form_params'] = $data;
                }
            }

            $response = $this->httpClient->request($method, $endpoint, $options);

            return $this->parseResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiException(
                'HTTP request failed: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function get(string $endpoint, array $params = []): array
    {
        // For akquisemanager API, the endpoint needs to be passed as apicall parameter
        $url = '?apicall='.$endpoint;
        if (! empty($params)) {
            $url .= '&'.http_build_query($params);
        }

        return $this->request('GET', $url);
    }

    public function post(string $endpoint, array $params, array $data = []): array
    {
        // For akquisemanager API, the endpoint needs to be passed as apicall parameter
        $url = '?apicall='.$endpoint;
        if (! empty($params)) {
            $url .= '&'.http_build_query($params);
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
            // Log the actual response for debugging
            error_log('API Response: '.substr($bodyContent, 0, 500));
            error_log('Response Content-Type: '.$response->getHeader('Content-Type')[0] ?? 'not set');

            throw new ApiException(
                'Invalid JSON response: '.json_last_error_msg()
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
