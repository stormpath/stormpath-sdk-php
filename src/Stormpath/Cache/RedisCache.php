<?php namespace Stormpath\Cache;


class RedisCache implements Cache {

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        // TODO: Implement put() method.
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }

    /**
     * Check existence of a key in cache.
     *
     * @param string $key
     * @return void
     */
    public function has($key)
    {
        // TODO: Implement has() method.
    }
}