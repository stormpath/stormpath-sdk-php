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

class Expansion {

    private $properties;

    public function __construct()
    {
        $this->properties = array();
    }

    /**
     * Adds an expansion property.
     * @param string $name the property name.
     * @param array $options <p>with the offset
     * and/or limit, if it applies. The offset
     * and/or limit must be the key(s) and the
     * value should be the desired number.
     * For example: $exp->addProperty('groups', array('offset' => 2, 'limit' =>10));
     * </p>
     * @return $this for method chaining.
     */
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

    /**
     * <p>Creates and return an expansion array, where the
     * key is the expansion keyword (expand), and the value
     * is the resulting string of all the added properties.</p>
     * @return array the constructed array.
     */
    public function toExpansionArray()
    {
        return array(Stormpath::EXPAND => strval($this));
    }

    /**
     * <p>Creates an Expansion object from an array.</p>
     *
     * @param array $expansions <p>If the property does not have offset or
     * limit, the property name must be the value; if it does, the
     * key must be the property name and the value must be a nested
     * array with 'offset' and/or 'limit' as key(s) and
     * the number(s) as value(s).</p>
     * @return Expansion the created expansion object.
     */
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

    /**
     * Creates a formatted expansion string, without the 'expand' keyword.
     * @return string the formatted expansion string, without the 'expand' keyword.
     * @throws \InvalidArgumentException if no properties were added to the expansion object.
     */
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