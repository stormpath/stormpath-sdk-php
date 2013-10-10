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
abstract class AbstractCollectionResource extends Resource implements \IteratorAggregate
{
    const OFFSET = "offset";
    const LIMIT  = "limit";
    const ITEMS  = "items";

    abstract function getItemClassName();

    protected function getOffset()
    {
        return $this->getProperty(self::OFFSET);
    }

    protected function getLimit()
    {
        return $this->getProperty(self::LIMIT);
    }

    protected function getCurrentPage()
    {
        $values = $this->getProperty(self::ITEMS);
        $items = $this->toResourceArray($values);

        return new Page($this->getOffset(), $this->getLimit(), $items);
    }

    protected function toResource($className, \stdClass $properties)
    {
        return $this->getDataStore()->instantiate($className, $properties);
    }

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

    public function getIterator()
    {
        return new \ArrayIterator($this->getCurrentPage()->getItems());
    }
}
