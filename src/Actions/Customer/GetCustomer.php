<?php

declare(strict_types=1);

namespace Ameax\AmApi\Actions\Customer;

use Ameax\AmApi\Actions\AbstractAction;

class GetCustomer extends AbstractAction
{
    public function execute(array $data = []): mixed
    {
        if (!isset($data['customer_id'])) {
            throw new \InvalidArgumentException('customer_id is required');
        }

        return $this->get('getCustomer', [
            'customer_id' => $data['customer_id']
        ]);
    }
}