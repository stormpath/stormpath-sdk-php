<?php

namespace Stormpath\Resource;

/*
 * Copyright 2016 Stormpath, Inc.
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

use Stormpath\Stormpath;
use Stormpath\Util\Magic;

class Search extends Magic {

    private $properties;
    private $filter;

    public function __construct()
    {
        parent::__construct();
        $this->properties = array();
        $this->filter = '';
    }

    /**
     * Adds a 'starts with' criteria, from a property
     * name and a value, in the format of:
     * 'propertyName=value*'
     *
     * @param string $name the property name.
     * @param string $search the search value.
     * @return $this for method chaining.
     */
    public function addStartsWith($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "$search*"));
        return $this;
    }

    /**
     * Adds an 'ends with' criteria, from a property
     * name and a value, in the format of:
     * 'propertyName=*value'
     *
     * @param string $name the property name.
     * @param string $search the search value.
     * @return $this for method chaining.
     */
    public function addEndsWith($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "*$search"));
        return $this;
    }

    /**
     * Adds an 'equals' criteria, from a property
     * name and a value, in the format of:
     * 'propertyName=value'
     *
     * @param string $name the property name.
     * @param string $search the search value.
     * @return $this for method chaining.
     */
    public function addEquals($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "$search"));
        return $this;
    }

    /**
     * Adds a 'match anywhere' criteria, from a property
     * name and a value, in the format of:
     * 'propertyName=*value*'
     *
     * @param string $name the property name.
     * @param string $search the search value.
     * @return $this for method chaining.
     */
    public function addMatchAnywhere($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "*$search*"));
        return $this;
    }

    /**
     * Sets the filter of the search: q=value.
     *
     * @param $filter the filter to set.
     * @return $this for method chaining.
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Creates an array with all the properties that were
     * set in the object, with the keys being the property names (or filter keyword 'q'),
     * their the corresponding values.
     * @return array the created array.
     * @throws \InvalidArgumentException if no properties were added to the object.
     */
    public function toSearchArray()
    {
        $filter = $this->filter;
        if (!is_numeric($filter) and !is_bool($filter) and !$filter and !$this->properties)
        {
            throw new \InvalidArgumentException("At least one search criteria or a filter is required to convert the search to array.");
        }

        $searchArray = array();

        if ($filter or is_bool($filter) or is_numeric($filter))
        {
            $searchArray[Stormpath::FILTER] = $filter;
        }

        return array_merge($searchArray, $this->properties);
    }

    /**
     * Creates the string representation of the search.
     * @return string the string representation of the object,
     * with the key=value pairs separated by '&'.
     */
    public function __toString()
    {
        $str = '';
        foreach($this->toSearchArray() as $key => $value)
        {
            $str .= $str ? '&' : $str;
            $str .= $key . '=' . $value;
        }

        return $str;
    }

}