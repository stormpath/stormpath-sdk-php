<?php namespace Stormpath\Cache;

use Memcached;

class MemcachedCache implements Cache {

    private $memcached;

    public function __construct($options)
    {
        $this->memcached = new Memcached();
        $this->memcached->addServers($options['memcached']);


    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        $value = $this->memcached->get($key);
        if ($this->memcached->getResultCode() == 0)
        {
            return $value;
        }

    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $minutes //memcached runs in seconds by default so this will be multiplied by 60
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        $this->memcached->set($key, $value, $minutes * 60 );
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function clear()
    {
        $this->memcached->flush();
    }

}