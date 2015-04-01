<?php namespace Stormpath\Resource;


use Stormpath\Stormpath;

class CustomData extends AbstractCollectionResource implements Saveable {
    const PATH = 'customData';

    public function __get($key)
    {
       return $this->getProperty($key);
    }

    public function __set($key, $value)
    {
        $this->setProperty($key, $value);
    }

    public function getItemClassName()
    {
        return Stormpath::CUSTOM_DATA;
    }

    public function save()
    {
        return $this->getDataStore()->save($this);
    }

    public function delete()
    {
        return $this->getDataStore()->delete($this);
    }

    public function remove($key)
    {
        return $this->getDataStore()->removeCustomDataItem($this, $key);
    }


}