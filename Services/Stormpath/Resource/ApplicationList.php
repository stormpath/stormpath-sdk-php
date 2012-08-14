<?php


class Services_Stormpath_Resource_ApplicationList
    extends Services_Stormpath_Resource_AbstractCollectionResource
{
    function getItemClassName()
    {
        return Services_Stormpath::APPLICATION;
    }

}
