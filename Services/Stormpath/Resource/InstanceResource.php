<?php


class Services_Stormpath_Resource_InstanceResource
    extends Services_Stormpath_Resource_Resource
    implements Services_Stormpath_Resource_Saveable
{
    public function save()
    {
        $this->getDataStore()->save($this);
    }

}
