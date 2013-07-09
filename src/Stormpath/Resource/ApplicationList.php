<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class ApplicationList extends AbstractCollectionResource
{
    function getItemClassName()
    {

        return StormpathService::APPLICATION;
    }

}
