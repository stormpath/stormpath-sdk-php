<?php
/**
 * Copyright 2017 Stormpath, Inc.
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

namespace Stormpath\Cache;

use Cache\Adapter\Common\AbstractCachePool;

class CachePSR6Adapter extends AbstractCachePool
{
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    protected function fetchObjectFromCache($key)
    {
        if (false === $result = unserialize($this->cache->get($key))) {
            return [false, null];
        }

        return $result;
    }

    protected function clearAllObjectsFromCache()
    {
        return $this->cache->clear();
    }

    protected function clearOneObjectFromCache($key)
    {
        return $this->cache->delete($key);
    }

    protected function storeItemInCache(CacheItemInterface $item, $ttl)
    {
        $key = $item->getKey();
        $data = serialize([true, $item->get()]);

        return $this->cache->put($key, $data, $ttl / 60);
    }
}
