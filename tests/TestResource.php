<?php


class TestResource extends Services_Stormpath_Resource_Resource
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
