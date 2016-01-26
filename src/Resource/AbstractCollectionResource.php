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

abstract class AbstractCollectionResource extends Resource implements \IteratorAggregate
{
    const OFFSET = Stormpath::OFFSET;
    const LIMIT  = Stormpath::LIMIT;
    const SIZE   = Stormpath::SIZE;
    const ITEMS  = "items";

    public function getOffset()
    {
        return $this->getProperty(self::OFFSET);
    }

    public function getLimit()
    {
        return $this->getProperty(self::LIMIT);
    }

    /**
     * Allows you to get the size (count) of number of returned items in
     * the paginated list of returned resource list.
     * @return int Number of returned items in the full paginated list
     * @since 1.9.0.beta
     */
    public function getSize()
    {
        return $this->getProperty(self::SIZE);
    }

    public function getCurrentPage()
    {
        $values = $this->getProperty(self::ITEMS);
        $items = $this->toResourceArray($values);

        return new Page($this->offset, $this->limit, $items);
    }

    /**
     * Sets the search criteria to this collection resource.
     * @param Search object|string|array $search <p>
     * The search statement. If it's a string, it
     * is considered a filter (q=$search); so in this case,
     * a string should never contain the filter key (q) nor
     * a resource property name.
     * </p>
     * <p>If it's an array, the keys must be the resource
     * property names, and the values must be the search criteria
     * including the wildcard (*), if desired.</p>
     * @return $this for method chaining.
     */
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
        $this->options = array_replace($this->options, array(self::OFFSET => $offset));
        return $this;
    }

    public function setLimit($limit)
    {
        $this->options = array_replace($this->options, array(self::LIMIT => $limit));
        return $this;
    }

    /**
     * Sets the order statement to this collection resource.
     * @param Order object|string $statement <p>
     * The order statement. If it's a string,
     * it should not contain the 'orderBy' keyword.
     * </p>
     * @return $this for method chaining.
     */
    public function setOrder($statement)
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

    /**
     * Sets the expansion criteria for this collection resource.
     * @param Expansion object|array|string $expansion<p>
     * If it's an array, and the property does not have offset or
     * limit, the property name and the value must be a nested
     * array with 'offset' and/or 'limit' as key(s) and
     * the number(s) as value(s).</p>
     * <p>If it's a string, it must not contain the 'expand' keyword.</p>
     * @return $this for method chaining.
     */
    public function setExpansion($expansion)
    {
        if ($expansion instanceof Expansion)
        {
            $this->options = array_replace($this->options, $expansion->toExpansionArray());

        } elseif (is_array($expansion))
        {
            $this->options = array_replace($this->options, Expansion::format($expansion)->toExpansionArray());

        } elseif (is_string($expansion))
        {
            $this->options = array_replace($this->options, array(Stormpath::EXPAND => $expansion));
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
