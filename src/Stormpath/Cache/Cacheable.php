<?php namespace Stormpath\Cache;

abstract class Cacheable {

    protected function resourceIsCacheable($resource)
    {
        $cache = true;

        // Check to see if it is a collection
        // All collections will have an items array in the data
        // If it is a collection, we do not want to cache it;
        if(isset($resource->items)) $cache = false;

        return $cache;

    }

    protected function addDataToCache($data)
    {
        $key = $data->href;
        $this->cache->put($key, $data, $this->cacheManager->options['ttl']);
    }

    protected function removeResourceFromCache($resource)
    {
        $this->cache->delete($resource->getHref());
    }


}