<?php namespace Stormpath\Cache;



class MemcachedCacheManager implements CacheManager {


    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * @return MemcachedCache
     * Privide region
     * cache per region
     * Singleton for the regions cache
     */
    public function getCache()
    {

        return new MemcachedCache($this->options);
    }




}