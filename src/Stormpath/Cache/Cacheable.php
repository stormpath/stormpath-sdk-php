<?php namespace Stormpath\Cache;

abstract class Cacheable {

    protected function resourceIsCacheable($resource)
    {
        $cache = true;

        // Check to see if it is a collection
        // All collections will have an items array in the data
        // If it is a collection, we do not want to cache it;
        if(isset($resource->items)) $cache = false;

        // We dont want to cache if it does not have a href.
        if(!isset($resource->href)) $cache = false;

        return $cache;

    }

    protected function isResourceCached($href, $options = array())
    {
        $key = $this->createKey($href, $options);
        $data = $this->cache->get($key);

        return $data;
    }

    protected function addDataToCache($data, $options = array())
    {


        $key = $this->createKey($data->href, $options);
        $this->cache->put($key, $data, $this->cacheManager->options['ttl']);
    }

    protected function removeResourceFromCache($resource)
    {
        $this->cache->delete($resource->getHref());

        $this->deleteExpanded($resource->getHref());
    }

    protected function removeCustomDataItemFromCache($resource, $key)
    {
        $cache = $this->cache->get($resource->getHref());
        unset($cache->$key);
    }

    private function createKey($href, $options)
    {
        $key = $href;
        if(!empty($options)) {
            $key .= ':' . implode(':',$options);
        }

        return $key;
    }

    private function deleteExpanded($href)
    {
        $all = $this->cache->all();
        if(empty($all)) return;

        $keys = array_keys($all);

        foreach($keys as $key) {
            if(strstr($key,$href)) {
                $this->cache->delete($key);
            }

        }
    }


}