<?php

declare(strict_types=1);

namespace Ameax\AmApi\Actions\Account;

use Ameax\AmApi\Actions\AbstractAction;

class ExecuteAccountLogin extends AbstractAction
{
    public function execute(array $data = []): mixed
    {
        // Handle login with password
        if (isset($data['login']) && isset($data['pw'])) {
            return $this->get('executeAccountLogin', [
                'login' => $data['login'],
                'pw' => $data['pw'],
            ]);
        }

        // Handle login without password (skip password check)
        if (isset($data['account_id']) && isset($data['skip_password_check'])) {
            return $this->get('executeAccountLogin', [
                'account_id' => $data['account_id'],
                'skip_password_check' => true,
            ]);
        }

        throw new \InvalidArgumentException('Either login/pw or account_id/skip_password_check is required');
    }
}