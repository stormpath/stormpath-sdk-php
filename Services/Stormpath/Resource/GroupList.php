<?php


class Services_Stormpath_Resource_GroupList
    extends Services_Stormpath_Resource_AbstractCollectionResource
{
    function getItemClassName()
    {
        return Services_Stormpath::GROUP;
    }

}
