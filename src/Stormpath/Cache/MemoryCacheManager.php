<?php namespace Stormpath\Cache;


class MemoryCacheManager implements CacheManager {

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getCache()
    {
        return new MemoryCache();
    }
}