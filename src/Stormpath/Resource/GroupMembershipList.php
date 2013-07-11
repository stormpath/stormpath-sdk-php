<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class GroupMembershipList extends AbstractCollection
{
    function getItemClassName()
    {
        return StormpathService::GROUP_MEMBERSHIP;
    }
}