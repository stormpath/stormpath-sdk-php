<?php


class Services_Stormpath_Resource_ResourceError extends RuntimeException
{
    private $error;

    public function __construct(Services_Stormpath_Resource_Error $error)
    {
        parent::__construct(($error->getMessage()) ? $error->getMessage() : '');
        $this->error = $error;
    }

    public function getStatus()
    {
        return $this->error ? $this->error->getStatus() : -1;
    }

    public function getErrorCode()
    {
        return $this->error ? $this->error->getCode() : -1;
    }

    public function getDeveloperMessage()
    {
        return $this->error ? $this->error->getDeveloperMessage() : null;
    }

    public function getMoreInfo()
    {
        return $this->error ? $this->error->getMoreInfo() : null;
    }
}
