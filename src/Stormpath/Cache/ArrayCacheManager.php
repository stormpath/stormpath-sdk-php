<?php namespace Stormpath\Cache;


class ArrayCacheManager implements CacheManager {

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getCache()
    {
        return new ArrayCache();
    }
}