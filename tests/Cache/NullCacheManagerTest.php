<?php

namespace Stormpath\Tests\Cache;


class NullCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\NullCacheManager(array());

        $this->assertInstanceOf("Stormpath\\Cache\\NullCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        $cacheManager = new \Stormpath\Cache\NullCacheManager(array());

        $cache = $cacheManager->getCache();

        $this->assertInstanceOf("Stormpath\\Cache\\NullCache", $cache);
    }
}