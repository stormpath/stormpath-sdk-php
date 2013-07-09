<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class GroupList extends AbstractCollectionResource
{
    function getItemClassName()
    {

        return StormpathService::GROUP;
    }

}
