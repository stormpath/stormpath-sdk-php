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

    public function addStartsWith($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "$search*"));
        return $this;
    }

    public function addEndsWith($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "*$search"));
        return $this;
    }

    public function addEquals($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "$search"));
        return $this;
    }

    public function addMatchAnywhere($name, $search)
    {
        $this->properties = array_replace($this->properties, array($name => "*$search*"));
        return $this;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function toSearchArray()
    {
        $filter = $this->filter;
        if (!is_numeric($filter) and !is_bool($filter) and !$filter and !$this->properties)
        {
            throw new \IllegalStateException("At least one search criteria or a filter is required to convert the search to array.");
        }

        $searchArray = array();

        if ($filter or is_bool($filter) or is_numeric($filter))
        {
            $searchArray[Stormpath::FILTER] = $filter;
        }

        return array_merge($searchArray, $this->properties);
    }

    public function __toString()
    {
        return implode('&', $this->toSearchArray());
    }

}