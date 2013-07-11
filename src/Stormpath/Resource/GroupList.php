<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class GroupList extends AbstractCollection
{
    function getItemClassName()
    {
        return StormpathService::GROUP;
    }
}