<?php

namespace Stormpath\Authc;

use Stormpath\Resource\Resource;

class BasicLoginAttempt extends Resource
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
