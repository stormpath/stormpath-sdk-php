<?php

namespace Stormpath\Cache;

use Cache\Adapter\Common\AbstractCachePool;

/**
* 
*/
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
        $key  = $item->getKey();
        $data = serialize([true, $item->get()]);
        return $this->cache->put($key, $data, $ttl/60);
    }
}
