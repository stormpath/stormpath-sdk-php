<?php

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
 *
 */

namespace Stormpath\Resource;


use Stormpath\DataStore\InternalDataStore;

class PaginatedIterator implements \Iterator {

    private $collectionResource;
    private $currentPage;
    private $currentItemIndex;
    private $dataStore;
    private $options;

    public function __construct(AbstractCollectionResource $collectionResource, InternalDataStore $dataStore, array $options = array())
    {
        $this->collectionResource = $collectionResource;
        $this->currentPage = $collectionResource->getCurrentPage();
        $this->currentItemIndex = 0;
        $this->dataStore = $dataStore;
        $this->options = $options;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $items = $this->currentPage->getItems();
        return $items[$this->currentItemIndex];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->currentItemIndex;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->currentItemIndex;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        $items = $this->currentPage->getItems();
        $valid = $this->currentItemIndex < count($items);
        $pageLimit = $this->currentPage->getLimit();
        $exhaustedLimit = $this->currentItemIndex == $pageLimit;

        if (!$valid && $exhaustedLimit)
        {
            //if we're done with the current page, and we've exhausted the page limit (i.e. we've read a
            //full page), we will have to execute another request to check to see if another page exists.
            //We can't 'trust' the current page iterator to know if more results exist on the server since it
            //only represents a single page.

            //query for the next page (move the offset up):
            $offset = $this->currentPage->getOffset() + $pageLimit;

            $query = array('offset' => $offset, 'limit' => $pageLimit);

            $this->options = array_replace($this->options, $query);

            $nextResource = $this->dataStore->getResource($this->collectionResource->getHref(), get_class($this->collectionResource), $this->options);
            $nextPage = $nextResource->getCurrentPage();

            if (count($nextPage->getItems()))
            {
                $valid = true;
                //update to reflect the new page:
                $this->collectionResource = $nextResource;
                $this->currentPage = $nextPage;
                $this->currentItemIndex = 0;
            }
        }

        return $valid;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->currentItemIndex = 0;
    }

}