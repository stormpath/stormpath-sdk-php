<?php namespace Stormpath\Cache;


class MemoryCache implements Cache {

    protected $storage = array();

    public function __construct()
    {
        $this->storage = isset($_SESSION['STORMPATH_CACHE']) ? $_SESSION['STORMPATH_CACHE'] : $this->storage;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->storage))
        {
            return $this->storage[$key];
        }
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
        $this->storage[$key] = $value;
        $_SESSION['STORMPATH_CACHE'] = $this->storage;
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function delete($key)
    {
        unset($this->storage[$key]);
        $_SESSION['STORMPATH_CACHE'] = $this->storage;
        return true;
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function clear()
    {
        $this->storage = array();
        $_SESSION['STORMPATH_CACHE'] = $this->storage;
    }

}