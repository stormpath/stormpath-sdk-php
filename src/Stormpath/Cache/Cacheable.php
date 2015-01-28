<?php namespace Stormpath\Cache;

abstract class Cacheable {

    protected function addDataToCache($data, $href)
    {
        $key = $this->generateKey($href);
        $this->cache->put($key, $data, $this->cacheManager->options['ttl']);
    }

    protected function cachedData($href)
    {
        $key = $this->generateKey($href);
        return $this->cache->get($key);
    }

    protected function createCachableResource($resource, $parentHref)
    {
        $tenantKey = $this->generateKey($resource->tenant->href);
        $key = $tenantKey . str_replace('/', ':', $parentHref);


        $this->cache->delete($key);
    }

    protected function saveCachableResource($resource)
    {
        $tenantKey = $this->generateKey($resource->tenant->href);
        $key = $tenantKey . ':' . $this->getResourceType($resource->href);

        $this->cache->delete($key);
    }

    protected function deleteCachableResource($resource)
    {
        $tenantKey = $this->generateKey($resource->tenant->href);
        $key = $tenantKey . ':' . $this->getResourceType($resource->href);

        $this->cache->delete($key);
    }

    private function generateKey($href)
    {
        $href = explode('/', $href);
        $href = array_slice($href, 4);
        return implode(':', $href);
    }

    private function getResourceType($href)
    {
        $href = explode('/', $href);
        $href = array_slice($href, 4);
        return array_shift($href);
    }
}