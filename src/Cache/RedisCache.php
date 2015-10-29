<?php namespace Stormpath\Cache;

use Redis;

class RedisCache implements Cache {

    private $redis;

    public function __construct($options)
    {
        $this->redis = new Redis();
        $this->redis->connect($options['redis']['host']);

        //if($options['redis']['password'] != NULL) {
            $this->redis->auth($options['redis']['password']);
        //}
        $this->prefix = "stormpath/";

    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($key);
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
        $this->redis->set($key, $value, $minutes);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function delete($key)
    {
        $this->redis->delete($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function clear()
    {
        $this->redis->flushAll();
    }


}