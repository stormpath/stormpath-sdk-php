<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class ApplicationList extends AbstractCollection
{
    function getItemClassName()
    {
        return StormpathService::APPLICATION;
    }
}