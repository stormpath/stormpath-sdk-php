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
        if (array_key_exists($offsetName, $options) or array_key_exists($limitName, $options))
        {
            $offset = array_key_exists($offsetName, $options) ? $options[$offsetName] : false;
            $limit = array_key_exists($limitName, $options) ? $options[$limitName] : false;

            if ($offset and $limit)
            {
                $optString = "($offsetName:$offset,$limitName:$limit)";

            } elseif ($offset)
            {
                $optString = "($offsetName:$offset)";

            } else
            {
                $optString = "($limitName:$limit)";
            }

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

    public static function format(array $expansions)
    {
        $expansion = new Expansion;
        foreach($expansions as $key => $exp)
        {
            $expansion->addProperty(is_string($key) ? $key : $exp,
                                    is_array($exp) ? $exp : array($exp));
        }

        return $expansion;
    }

    public function __toString()
    {
        if (!$this->properties)
        {
            throw new \InvalidArgumentException("At least one property needs to be set to convert the expansion to string.");
        }

        $properties = $this->properties;
        $query = '';
        foreach($properties as $key => $prop)
        {
            $query .= $query ? ',' : $query;
            $query .= $key . $prop;
        }

        return $query;
    }

}