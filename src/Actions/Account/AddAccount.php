<?php

declare(strict_types=1);

namespace Ameax\AmApi\Actions\Account;

use Ameax\AmApi\Actions\AbstractAction;

class AddAccount extends AbstractAction
{
    public function execute(array $data = []): mixed
    {
        $response = $this->post('addAccount', [], $data);
        
        $this->checkForErrors($response);
        return $this->extractResult($response);
    }
}