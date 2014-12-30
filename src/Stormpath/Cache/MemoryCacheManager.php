<?php namespace Stormpath\Cache;


class MemoryCacheManager implements CacheManager {

    public function __construct()
    {
        session_start();
    }

    public function getCache()
    {
        return new MemoryCache();
    }
}