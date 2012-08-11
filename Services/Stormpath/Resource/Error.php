<?php

class Services_Stormpath_Resource_Error extends Services_Stormpath_Resource_Resource
{
    const STATUS      = "status";
    const CODE        = "code";
    const MESSAGE     = "message";
    const DEV_MESSAGE = "developerMessage";
    const MORE_INFO   = "moreInfo";

    public function __construct(stdClass $properties)
    {
        parent::__construct(null, $properties);
    }

    public function getStatus()
    {
        //TODO:implement
    }

    public function getCode()
    {
        //TODO:implement
    }

    public function getMessage()
    {
        //TODO:implement
    }

    public function getDeveloperMessage()
    {
        //TODO:implement
    }

    public function getMoreInfo()
    {
        //TODO:implement
    }

}
