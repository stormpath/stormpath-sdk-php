<?php

namespace Stormpath\Resource;

class Instance extends Resource implements SaveableInterface
{
	/**
     * Save the instance of this resource to the DataStore
     */
	public function save()
    {
    	$this->getDataStore()->save($this);
    }
    
    /**
     * Delete the instance of this resource from the DataStore
     */
	public function delete()
	{
		$this->getDataStore()->delete($this);
	}
}