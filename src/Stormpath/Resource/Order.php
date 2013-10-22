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

class Order extends Magic {

    private $properties;
    private $sort;

    public function __construct(array $properties = array(), $sort = null)
    {
        parent::__construct();
        $this->properties = $properties;
        foreach($properties as $prop)
        {
            $this->addProperty($prop);
        }

        $this->setSort($sort);
    }

    public function addProperty($name)
    {
        if ($name)
        {
            $this->properties = array_replace($this->properties, array($name => $name));
        }

        return $this;
    }

    public function setSort($sort)
    {
        if (array_key_exists($sort, Stormpath::$Sorts))
        {
            $this->sort = Stormpath::$Sorts[$sort];
        }

        return $this;
    }

    public function toOrderArray()
    {
        return array(Stormpath::ORDER_BY => strval($this));
    }

    public function toOrderString()
    {
        return Stormpath::ORDER_BY . '=' . strval($this);
    }

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