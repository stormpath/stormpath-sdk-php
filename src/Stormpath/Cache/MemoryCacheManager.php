<?php namespace Stormpath\Cache;


class MemoryCacheManager implements CacheManager {

    public function getCache()
    {
        return new MemoryCache();
    }
}