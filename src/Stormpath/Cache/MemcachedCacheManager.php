<?php namespace Stormpath\Cache;



class MemcachedCacheManager implements CacheManager {


    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getCache()
    {

        return new MemcachedCache($this->options);
    }




}