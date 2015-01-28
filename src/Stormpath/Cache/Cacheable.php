<?php namespace Stormpath\Cache;

abstract class Cacheable {

    protected function addDataToCache($data, $href)
    {
        $key = $this->generateKey($href);
        $this->cache->put($key, $data, 1);
    }

    protected function cachedData($href)
    {
        $key = $this->generateKey($href);
        return $this->cache->get($key);
    }

    private function generateKey($href)
    {
        $href = explode('/', $href);
        $href = array_slice($href, 4);
        return implode(':', $href);
    }
}