<?php


class Services_Stormpath_Resource_AccountList
    extends Services_Stormpath_Resource_AbstractCollectionResource
{
    function getItemClassName()
    {
        return Services_Stormpath::ACCOUNT;
    }

}
