<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class AccountList extends AbstractCollection
{
    function getItemClassName()
    {
        return StormpathService::ACCOUNT;
    }
}