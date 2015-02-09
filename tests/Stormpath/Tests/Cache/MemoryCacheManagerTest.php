<?php

namespace Stormpath\Tests\Cache;


class MemoryCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\MemoryCacheManager(array());

        $this->assertInstanceOf("Stormpath\\Cache\\MemoryCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        $cacheManager = new \Stormpath\Cache\MemoryCacheManager(array());

        $cache = $cacheManager->getCache();

        $this->assertInstanceOf("Stormpath\\Cache\\MemoryCache", $cache);
    }
}