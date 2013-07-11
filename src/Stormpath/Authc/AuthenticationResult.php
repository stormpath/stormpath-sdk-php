<?php

namespace Stormpath\Authc;

use Stormpath\Resource\Resource;
use Stormpath\Service\StormpathService;

class AuthenticationResult extends Resource
{
    const ACCOUNT = "account";

    public function getAccount()
    {
        return $this->getResourceProperty(self::ACCOUNT, StormpathService::ACCOUNT);
    }
}
