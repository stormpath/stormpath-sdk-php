<?php


class Services_Stormpath_Resource_DirectoryList
    extends Services_Stormpath_Resource_AbstractCollectionResource
{
    function getItemClassName()
    {
        return Services_Stormpath::DIRECTORY;
    }

}
