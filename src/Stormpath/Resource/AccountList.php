<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class AccountList extends AbstractCollectionResource
{
    function getItemClassName()
    {
        return StormpathService::ACCOUNT;
    }

}
