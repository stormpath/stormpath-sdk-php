<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class DirectoryList extends AbstractCollectionResource
{
    function getItemClassName()
    {
        return StormpathService::DIRECTORY;
    }

}
