<?php namespace Stormpath\Cache;


class NullCacheManager implements CacheManager {

    public function getCache()
    {
        return new NullCache();
    }
}