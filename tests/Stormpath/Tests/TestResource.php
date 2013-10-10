<?php

namespace Stormpath\Tests;

class TestResource extends \Stormpath\Resource\Resource
{

    public function getName()
    {
        return $this->getProperty('name');
    }

    public function setName($name)
    {
        $this->setProperty('name', $name);
    }

    public function getDescription()
    {
        return $this->getProperty('description');
    }

    public function setDescription($description)
    {
        $this->setProperty('description', $description);
    }
}
