<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class GroupMembershipList extends AbstractCollectionResource
{
    function getItemClassName()
    {

        return StormpathService::GROUP_MEMBERSHIP;
    }

}
