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

abstract class AbstractCollectionResource extends Resource implements \IteratorAggregate
{
    const OFFSET = Stormpath::OFFSET;
    const LIMIT  = Stormpath::LIMIT;
    const ITEMS  = "items";

    public function getOffset()
    {
        return $this->getProperty(self::OFFSET);
    }

    public function getLimit()
    {
        return $this->getProperty(self::LIMIT);
    }

    public function getCurrentPage()
    {
        $values = $this->getProperty(self::ITEMS);
        $items = $this->toResourceArray($values);

        return new Page($this->offset, $this->limit, $items);
    }

    public function setSearch($search)
    {
        $searchArr = array();
        if ($search instanceof Search)
        {
            $searchArr = $search->toSearchArray();

        }elseif (is_string($search))
        {
            $searchArr[Stormpath::FILTER] = $search;

        } elseif (is_array($search))
        {
            $searchArr = $search;
        }

        $this->options = array_replace($this->options, $searchArr);
        return $this;
    }

    public function setOffset($offset)
    {
        $this->options = array_replace($this->options, array(OFFSET, $offset));
        return $this;
    }

    public function setLimit($limit)
    {
        $this->options = array_replace($this->options, array(LIMIT, $limit));
        return $this;
    }

    public function setOrder(array $statement)
    {
        if ($statement instanceof Order)
        {
            $this->options = array_replace($this->options, $statement->toOrderArray());

        } elseif (is_string($statement))
        {
            $this->options = array_replace($this->options, array(Stormpath::ORDER_BY => $statement));
        }

        return $this;
    }

    abstract function getItemClassName();

    private function toResourceArray(array $values)
    {
        $className = $this->getItemClassName();
        $resourceArray = array();

        $i = 0;
        foreach($values as $value)
        {
            $resource = $this->toResource($className, $value);
            $resourceArray[$i] = $resource;
            $i++;
        }

        return $resourceArray;
    }

    protected function toResource($className, \stdClass $properties)
    {
        return $this->dataStore->instantiate($className, $properties);
    }

    public function getIterator()
    {
        return new PaginatedIterator($this, $this->dataStore, $this->options);
    }
}
