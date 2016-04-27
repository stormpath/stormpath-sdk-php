<?php

namespace Stormpath\Tests\Cache;


class RedisCacheManagerTest extends \PHPUnit_Framework_TestCase {

    public function testIsInstanciable()
    {
        $cacheManager = new \Stormpath\Cache\RedisCacheManager();

        $this->assertInstanceOf("Stormpath\\Cache\\RedisCacheManager", $cacheManager);
    }

    public function testCanGetCache()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped(
              'The Redis extension is not available.'
            );
        }

        $cacheManager = new \Stormpath\Cache\RedisCacheManager();

        $redisHost = getenv('REDIS_HOST') ?: '127.0.0.1';
        $redisPort = getenv('REDIS_PORT') ?: '6379';
        $redisPassword = getenv('REDIS_PASSWORD') ?: null;

        $cache = $cacheManager->getCachePool(array('redis'=>array('host'=>$redisHost, 'port'=>$redisPort, 'password'=>$redisPassword)));

        $this->assertInstanceOf("Psr\\Cache\\CacheItemPoolInterface", $cache);
    }
}
