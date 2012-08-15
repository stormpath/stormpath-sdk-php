<?php


class Services_Stormpath_Authc_BasicLoginAttempt
    extends Services_Stormpath_Resource_Resource
{
    const TYPE = "type";
    const VALUE = "value";

    public function getType()
    {
        return $this->getProperty(self::TYPE);
    }

    public function setType($type)
    {
        $this->setProperty(self::TYPE, $type);
    }

    public function getValue()
    {
        return $this->getProperty(self::VALUE);
    }

    public function setValue($value)
    {
        $this->setProperty(self::VALUE, $value);
    }
}
