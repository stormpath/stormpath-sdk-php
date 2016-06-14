<?php

namespace Stormpath\Tests\Cache;


class MemcachedCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\MemcachedCacheManager();

        $this->assertInstanceOf("Stormpath\\Cache\\MemcachedCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped(
              'The Memcached extension is not available.'
            );
        }

        $cacheManager = new \Stormpath\Cache\MemcachedCacheManager();

        $memcachedHost = getenv('MEMCACHED_HOST') ?: '127.0.0.1';
        $memcachedPort = getenv('MEMCACHED_PORT') ?: '11211';
        $memcachedPassword = getenv('MEMCACHED_PASSWORD') ?: null;

        $options = array('memcached'=>array(array('host'=>$memcachedHost, 'port'=>$memcachedPort, 'password'=>$memcachedPassword)));

        $cache = $cacheManager->getCachePool($options);

        $this->assertInstanceOf("Psr\\Cache\\CacheItemPoolInterface", $cache);
    }
}
