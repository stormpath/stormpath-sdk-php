<?php

namespace Stormpath\Resource;

/*
 * Copyright 2013 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Stormpath\DataStore\InternalDataStore;

class Resource
{
    private $dataStore;
    private $properties;
    private $dirtyProperties;
    private $materialized;
    private $dirty;

    /** @var array Hash of methods available to the class (provides fast isset() lookups) */
    private  $methods;

    const HREF_PROP_NAME = "href";

    public function __construct(InternalDataStore $dataStore = null, \stdClass $properties = null)
    {
        $this->dataStore = $dataStore;
        $this->setProperties($properties);
        $this->methods = array_flip(get_class_methods(get_class($this)));
    }

    public function setProperties(\stdClass $properties = null)
    {
        $this->dirty = false;

        $this->properties = new \stdClass;
        $this->dirtyProperties = new \stdClass;

        if ($properties)
        {
            $this->properties = $properties;
            $propertiesArr = (array) $properties;
            $hrefOnly = count($propertiesArr) == 1 and array_key_exists(self::HREF_PROP_NAME, $propertiesArr);
            $this->materialized = !$hrefOnly;
        } else
        {
            $this->materialized = false;
        }
    }

    public function getProperty($name, array $options = array())
    {
        if (self::HREF_PROP_NAME != $name)
        {
            //not the href/id, must be a property that requires materialization:
            if (!$this->isNew() and !$this->isMaterialized())
            {
                // only materialize if the property hasn't been set previously (no need to execute a server
                // request since we have the most recent value already):
                $present = isset($this->dirtyProperties->$name);

                if (!$present)
                {
                    // exhausted present properties - we require a server call:
                    $this->materialize($options);
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

    /**
     * Magic "get" method
     *
     * @param string $property Property name
     * @return mixed|null Property value if it exists, null if not
     */
    public function __get($property) {

        $method = 'get' .ucfirst($property);
        if (isset($this->methods[$method])) {
            return $this->{$method}();
        }

        return null;
    }

    /**
     * Magic "set" method
     *
     * @param string $property Property name
     * @param mixed $value Property value
     */
    public function __set($property, $value)
    {
        $method = 'set' .ucfirst($property);
        if (isset($this->methods[$method])) {
            $this->{$method}($value);
        }
    }

    protected function getResourceProperty($key, $className, array $options = array())
    {
        $value = $this->getProperty($key, $options);

        $href = self::HREF_PROP_NAME;

        if ($value instanceof \stdClass)
        {
            $href = $value->$href;

        } else
        {
            $href = false;
        }

        if ($href)
        {
            return $this->dataStore->instantiate($className, $value);
        }
    }

    protected function setResourceProperty($name, Resource $resource) {

        $href = $resource->getHref();
        $properties = new \stdClass();
        $properties->href = $href;
        $this->setProperty($name, $properties);
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

    protected function materialize(array $options = array())
    {
        $className = get_class($this);

        $resource = $this->dataStore->getResource($this->getHref(), $className, $options);

        $this->properties = $resource->properties;

        //retain dirty properties:
        $this->properties = (object) array_merge((array)$this->properties, (array)$this->dirtyProperties);

        $this->materialized = true;
    }

    /**
     * Returns {@code true} if the resource does not yet have an assigned 'href' property, {@code false} otherwise.
     *
     * @return {@code true} if the resource does not yet have an assigned 'href' property, {@code false} otherwise.
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
        return property_exists($this->properties, $name) ? $this->properties->$name : null;
    }

}
