<?php


class Services_Stormpath_Resource_GroupMembershipList
    extends Services_Stormpath_Resource_AbstractCollectionResource
{
    function getItemClassName()
    {
        return Services_Stormpath::GROUP_MEMBERSHIP;
    }

}
