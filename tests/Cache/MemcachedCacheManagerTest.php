<?php

namespace Stormpath\Tests\Cache;


class MemcachedCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\MemcachedCacheManager(array('memcached'=>array(array('host'=>'127.0.0.1', 'port'=>'11211', 'password'=>null))));

        $this->assertInstanceOf("Stormpath\\Cache\\MemcachedCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        $cacheManager = new \Stormpath\Cache\MemcachedCacheManager(array('memcached'=>array(array('host'=>'127.0.0.1', 'port'=>'11211', 'password'=>null))));

        $cache = $cacheManager->getCache();

        $this->assertInstanceOf("Stormpath\\Cache\\MemcachedCache", $cache);
    }
}