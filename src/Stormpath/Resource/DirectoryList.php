<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class DirectoryList extends AbstractCollection
{
    function getItemClassName()
    {
        return StormpathService::DIRECTORY;
    }
}