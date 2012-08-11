<?php


abstract class Services_Stormpath_Resource_Resource
{
    private $dataStore;
    private $properties;
    private $materialized;
    private $dirty;

    const HREF_PROP_NAME = "href";

    public function __construct(Services_Stormpath_DataStore_InternalDataStore $dataStore = null,
                                stdClass $properties = null)
    {
        $this->dataStore = $dataStore;
        $this->properties = $properties;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperties(stdClass $properties)
    {
        $this->dirty = false;

        if ($properties)
        {
            $this->properties = $properties;
            $propertiesArr = (array) $properties;
            $hrefOnly = count($propertiesArr) == 1 and $propertiesArr[self::HREF_PROP_NAME];
            $this->materialized = !$hrefOnly;
        } else
        {
            $this->materialized = false;
        }
    }

    public function getProperty($name)
    {
        if (self::HREF_PROP_NAME != $name)
        {
            //not the href/id, must be a property that requires materialization:
            if(!$this->isNew() and !$this->isMaterialized())
            {
                $this->materialize();
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

    protected function getResourceProperty($key, $className)
    {
        $value = $this->getProperty($key);

        $href = self::HREF_PROP_NAME;

        if ($value instanceof stdClass)
        {
            $href = $value->$href;

        } else
        {
            unset($href);
        }


        if ($href)
        {
            return $this->dataStore->instantiate($className, $value);
        }
    }

    protected function setProperty($name, $value)
    {
        if ($value)
        {
            $this->properties->$name = $value;
            $this->dirty = true;
        } else
        {
            if (isset($this->properties->$name))
            {
                unset($this->properties->$name);
                $this->dirty = true;
            }
        }
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
        $this->properties = $resource->getProperties();
        $this->materialized = true;
    }

    /**
     * Returns {@code true} if the resource doesn't yet have an assigned 'href' property, {@code false} otherwise.
     *
     * @return {@code true} if the resource doesn't yet have an assigned 'href' property, {@code false} otherwise.
     */
     protected function isNew() {

        //we can't call getHref() in here, otherwise we'll have an infinite loop:
        $prop = $this->readProperty(self::HREF_PROP_NAME);

         if ($prop)
         {
             return false;
         }

         return true;
    }

    private function readProperty($name)
    {
        return $this->getProperties()->$name;
    }

}
