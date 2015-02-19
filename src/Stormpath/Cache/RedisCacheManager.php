<?php namespace Stormpath\Cache;


class RedisCacheManager implements CacheManager {

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getCache()
    {

        return new RedisCache($this->options);
    }
}