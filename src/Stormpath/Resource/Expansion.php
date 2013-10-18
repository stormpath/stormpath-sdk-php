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

class Expansion {

    private $properties;

    public function __construct()
    {
        $this->properties = array();
    }

    public function addProperty($name, array $options = array())
    {

        $optCompound = array();
        $offsetName = Stormpath::OFFSET;
        $limitName = Stormpath::LIMIT;
        if ($options[$offsetName] or $options[$limitName])
        {
            $offset = $options[$offsetName];
            $limit = $options[$limitName];
            $optString = $offset and $limit ? "($offsetName:$offset,$limitName:$limit)" :
                         $offset ? "($offsetName:$offset)" : "($limitName:$limit)";
            $optCompound[$name] = $optString;
        } else
        {
            $optCompound[$name] = '';
        }

        $this->properties = array_replace($this->properties, $optCompound);

        return $this;
    }

    public function toExpansionArray()
    {
        return array(Stormpath::EXPAND => strval($this));
    }

    public function toExpansionString()
    {
        return Stormpath::EXPAND . '=' . strval($this);
    }

    public static function format(array $expansions)
    {
        $expansion = new Expansion;
        foreach($expansions as $exp)
        {
            $currentKey = key($expansion);
            $expansion->addProperty(is_string($currentKey) ? $currentKey : $exp,
                                    is_array($exp) ? $exp : array($exp));
        }

        return $expansion;
    }

    public function __toString()
    {
        if (!$this->properties)
        {
            throw new \IllegalStateException("At least one property needs to be set to convert the expansion to string.");
        }

        $query = '';

        foreach($this->properties as $prop)
        {
            $query .= $query ? ',' : $query;
            $query .= $this->properties[$prop] . $prop;
        }

        return $query;
    }

}