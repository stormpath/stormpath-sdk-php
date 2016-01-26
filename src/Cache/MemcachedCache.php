<?php namespace Stormpath\Cache;
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

use Memcached;

class MemcachedCache implements Cache {

    private $memcached;

    public function __construct($options)
    {
        $this->memcached = new Memcached();
        $this->memcached->addServers($options['memcached']);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {

        $value = $this->memcached->get($key);
        if ($this->memcached->getResultCode() == 0)
        {
            return $value;
        }

    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $minutes //memcached runs in seconds by default so this will be multiplied by 60
     * @return void
     */
    public function put($key, $value, $minutes)
    {

        $this->memcached->set($key, $value, $minutes * 60 );
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function clear()
    {
        $this->memcached->flush();
    }

}