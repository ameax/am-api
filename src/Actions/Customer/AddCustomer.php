<?php

declare(strict_types=1);

namespace Ameax\AmApi\Actions\Customer;

use Ameax\AmApi\Actions\AbstractAction;
use Ameax\AmApi\Http\AmApiClient;
use Ameax\AmApi\Mappers\CustomerMapper;

class AddCustomer extends AbstractAction
{
    public function __construct(
        AmApiClient $client,
        private readonly CustomerMapper $mapper
    ) {
        parent::__construct($client);
    }

    public function execute(array $data = []): mixed
    {
        $mappedData = $this->mapper->mapToApi($data);
        $response = $this->post('addCustomer', [], $mappedData);
        
        $this->checkForErrors($response);
        return $this->extractResult($response);
    }
}