<?php


class Services_Stormpath_Resource_ResourceError extends Exception
{
    private $error;

    public function __construct(Services_Stormpath_Resource_Error $error)
    {
        parent::__construct(($error->getMessage()) ? $error->getMessage() : '');
        $this->error = $error;
    }

    public function getStatus()
    {
        return $this->error->getStatus();
    }

    public function getErrorCode()
    {
        return $this->error->getCode();
    }

    public function getDeveloperMessage()
    {
        return $this->error->getDeveloperMessage();
    }

    public function getMoreInfo()
    {
        return $this->error->getMoreInfo();
    }
}
