<?php

namespace Stormpath\Resource;

use Stormpath\DataStore\InternalDataStore;

class Resource
{
    private $dataStore;
    private $properties;
    private $dirtyProperties;
    private $materialized;
    private $dirty;
    
    const HREF_PROP_NAME = "href";
    
    public function __construct(InternalDataStore $dataStore = null, stdClass $properties = null)
    {
        $this->dataStore = $dataStore;
        $this->setProperties($properties);
    }
    
    public function setProperties(stdClass $properties = null)
    {
        $this->dirty = false;
        
        $this->properties = new stdClass();
        $this->dirtyProperties = new stdClass();
        
        if($properties) {
            $this->properties = $properties;
            $propertiesArr = (array) $properties;
            $hrefOnly = count($propertiesArr) == 1 and array_key_exists(self::HREF_PROP_NAME, $propertiesArr);
            $this->materialized = !$hrefOnly;
        } else {
            $this->materialized = false;
        }
    }
    
    public function getProperty($name)
    {
        if (self::HREF_PROP_NAME != $name) {
            //not the href/id, must be a property that requires materialization:
            if (!$this->isNew() and !$this->isMaterialized()) {
                // only materialize if the property hasn't been set previously (no need to execute a server
                // request since we have the most recent value already):
                $present = isset($this->dirtyProperties->$name);
                
                if (!$present) {
                    // exhausted present properties - we require a server call:
                    $this->materialize();
                }
            }
        }
        
        return $this->readProperty($name);
    }

    public function getPropertyNames()
    {
        return array_keys((array) $this->properties);
    }
    
    public function getHref()
    {
        return $this->getProperty(self::HREF_PROP_NAME);
    }
    
    public function __toString()
    {
        return get_class($this);
    }
    
    protected function getResourceProperty($key, $className)
    {
        $value = $this->getProperty($key);
        
        $href = self::HREF_PROP_NAME;
		
        if ($value instanceof stdClass) {
            $href = $value->$href;
        } else {
            $href = false;
        }
		
        if ($href) {
            return $this->dataStore->instantiate($className, $value);
        }
    }
    
    protected function setProperty($name, $value)
    {
        $this->properties->$name = $value;
        $this->dirtyProperties->$name = $value;
        $this->dirty = true;
    }
    
    protected function getDataStore()
    {
        return $this->dataStore;
    }
    
    protected function isMaterialized()
    {
        return $this->materialized;
    }
    
    protected function materialize()
    {
        $className = get_class($this);
		
        $resource = $this->dataStore->getResource($this->getHref(), $className);
		
        $this->properties = $resource->properties;
		
        //retain dirty properties:
        $this->properties = (object) array_merge((array)$this->properties, (array)$this->dirtyProperties);
		
        $this->materialized = true;
    }
    
    /**
     * Returns {@code true} if the resource doesn't yet have an assigned 'href' property, {@code false} otherwise.
     *
     * @return bool
     */
    protected function isNew() {
		//we can't call getHref() in here, otherwise we'll have an infinite loop:
		$prop = $this->readProperty(self::HREF_PROP_NAME);
		if($prop) {
			return false;
		}
		
		return true;
	}
	
    private function readProperty($name)
    {
        return property_exists($this->properties, $name) ? $this->properties->$name : null;
    }

}