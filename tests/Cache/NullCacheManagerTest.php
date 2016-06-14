<?php

namespace Stormpath\Tests\Cache;


class NullCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\NullCacheManager();

        $this->assertInstanceOf("Stormpath\\Cache\\NullCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        $cacheManager = new \Stormpath\Cache\NullCacheManager();

        $cache = $cacheManager->getCachePool([]);

        $this->assertInstanceOf("Psr\\Cache\\CacheItemPoolInterface", $cache);
    }
}
