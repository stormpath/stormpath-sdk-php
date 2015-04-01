<?php namespace Stormpath\Resource;


use Stormpath\Client;
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
        var_dump($this->getDataStore()->save($this));
        die();
    }

    public function delete()
    {
        var_dump($this->getDataStore()->delete($this));
        die();
    }

    public function remove($key)
    {
        var_dump($this->getDataStore()->removeCustomDataItem($this, $key));
        die();
    }


}