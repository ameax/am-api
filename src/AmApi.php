<?php

declare(strict_types=1);

namespace Ameax\AmApi;

use Ameax\AmApi\Config\Config;
use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Resources\AccountResource;
use Ameax\AmApi\Resources\CustomerResource;
use Ameax\AmApi\Resources\PersonResource;
use Ameax\AmApi\Resources\RelationResource;
use Ameax\AmApi\Resources\UserResource;

class AmApi
{
    private AmApiClient $client;
    private ?CustomerResource $customerResource = null;
    private ?AccountResource $accountResource = null;
    private ?PersonResource $personResource = null;
    private ?RelationResource $relationResource = null;
    private ?UserResource $userResource = null;

    public function __construct(Config $config)
    {
        $this->client = new AmApiClient($config);
    }

    public function customers(): CustomerResource
    {
        if ($this->customerResource === null) {
            $this->customerResource = new CustomerResource($this->client);
        }

        return $this->customerResource;
    }

    public function accounts(): AccountResource
    {
        if ($this->accountResource === null) {
            $this->accountResource = new AccountResource($this->client);
        }

        return $this->accountResource;
    }

    public function persons(): PersonResource
    {
        if ($this->personResource === null) {
            $this->personResource = new PersonResource($this->client);
        }

        return $this->personResource;
    }

    public function relations(): RelationResource
    {
        if ($this->relationResource === null) {
            $this->relationResource = new RelationResource($this->client);
        }

        return $this->relationResource;
    }

    public function users(): UserResource
    {
        if ($this->userResource === null) {
            $this->userResource = new UserResource($this->client);
        }

        return $this->userResource;
    }

    /**
     * Get the underlying HTTP client for direct access if needed
     */
    public function getClient(): AmApiClient
    {
        return $this->client;
    }
}