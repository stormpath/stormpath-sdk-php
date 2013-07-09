<?php

namespace Stormpath\Resource;

class InstanceResource extends Resource
    implements Saveable
{
    public function save()
    {
        $this->getDataStore()->save($this);
    }

}
