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

class Order extends Magic {

    private $properties;
    private $sort;

    /**
     * Constructs the Order object.
     * @param null|array $properties optional argument containing
     * the property names.
     * @param null|string $sort optional property to determine the sorting (asc or desc).
     */
    public function __construct($properties = null, $sort = null)
    {
        parent::__construct();
        $this->properties = $properties ? $properties : array();

        if (is_array($properties))
        {
            foreach($properties as $prop)
            {
                $this->addProperty($prop);
            }
        }

        $this->setSort($sort);
    }

    /**
     * @param $name the property name of the order statement.
     * @return $this for method chaining.
     */
    public function addProperty($name)
    {
        if ($name)
        {
            $this->properties = array_replace($this->properties, array($name => $name));
        }

        return $this;
    }

    /**
     * Sets the sorting (asc or desc).
     *
     * @param string $sort the sorting (asc or desc).
     * @return $this for method chaining.
     */
    public function setSort($sort)
    {
        if (array_key_exists($sort, Stormpath::$Sorts))
        {
            $this->sort = Stormpath::$Sorts[$sort];
        }

        return $this;
    }

    /**
     * <p>Creates and return an order array, where the
     * key is the order keyword (orderBy), and the value
     * is the resulting string of all the added properties,
     * and the sorting (if any).</p>
     * @return array the constructed array.
     */
    public function toOrderArray()
    {
        return array(Stormpath::ORDER_BY => strval($this));
    }

    /**
     * Creates an Order object based on an array of property
     * names and an optional sort value.
     * @param array $properties the property names.
     * @param null|string $sort optional, asc or desc.
     * @return Order the created order object.
     */
    public static function format(array $properties, $sort= null)
    {
        $order = new Order();

        foreach($properties as $prop)
        {
            $order->addProperty($prop);
        }

        $order->setSort($sort);

        return $order;
    }

    /**
     * Creates a formatted order string, without the 'orderBy' keyword.
     * @return string the formatted order string, without the 'orderBy' keyword.
     * @throws \InvalidArgumentException if no properties were added to the order object.
     */
    public function __toString()
    {
        if (!$this->properties)
        {
            throw new \InvalidArgumentException("At least one property needs to be set to convert the order statement to string.");
        }

        $statement = implode(',', $this->properties);

        if ($this->sort)
        {
            $statement .= " $this->sort";
        }

        return $statement;
    }
}