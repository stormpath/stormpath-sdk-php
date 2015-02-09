<?php

namespace Stormpath\Tests\Cache;


class RedisCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\RedisCacheManager(array('redis'=>array('host'=>'127.0.0.1', 'port'=>'6379', 'password'=>null)));

        $this->assertInstanceOf("Stormpath\\Cache\\RedisCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        $cacheManager = new \Stormpath\Cache\RedisCacheManager(array('redis'=>array('host'=>'127.0.0.1', 'port'=>'6379', 'password'=>null)));

        $cache = $cacheManager->getCache();

        $this->assertInstanceOf("Stormpath\\Cache\\RedisCache", $cache);
    }
}