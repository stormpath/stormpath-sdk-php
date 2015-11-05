<?php

namespace Stormpath\Tests\Cache;


class ArrayCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\ArrayCacheManager(array());

        $this->assertInstanceOf("Stormpath\\Cache\\ArrayCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        $cacheManager = new \Stormpath\Cache\ArrayCacheManager(array());

        $cache = $cacheManager->getCache();

        $this->assertInstanceOf("Stormpath\\Cache\\ArrayCache", $cache);
    }
}