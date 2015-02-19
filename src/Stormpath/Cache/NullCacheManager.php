<?php namespace Stormpath\Cache;


class NullCacheManager implements CacheManager {

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getCache()
    {
        return new NullCache();
    }
}