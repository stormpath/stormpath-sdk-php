<?php namespace Stormpath\Cache;


class MemcachedCacheManager implements CacheManager {

    protected $servers = [];

    public function getCache()
    {
        return new MemcachedCache();
    }

    public function addServer($host, $port, $weight)
    {
        $this->servers[] = array('host' => $host, 'port' => $port, 'weight' => $weight);

        return $this;
    }
}