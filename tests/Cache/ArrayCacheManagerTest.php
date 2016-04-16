<?php

namespace Stormpath\Tests\Cache;


class ArrayCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\ArrayCacheManager();

        $this->assertInstanceOf("Stormpath\\Cache\\ArrayCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        $cacheManager = new \Stormpath\Cache\ArrayCacheManager();

        $cache = $cacheManager->getCachePool([]);

        $this->assertInstanceOf("Psr\\Cache\\CacheItemPoolInterface", $cache);
    }
}
