<?php

namespace Stormpath\Tests\Cache;


class RedisCacheTest extends \PHPUnit_Framework_TestCase {

    public $cache;

    public function setUp()
    {
        $cacheManager = new \Stormpath\Cache\RedisCacheManager(array('redis'=>array('host'=>'127.0.0.1', 'port'=>'6379', 'password'=>null)));

        $this->cache = $cacheManager->getCache();

    }

    public function testCanPutCache()
    {
        $this->cache->put('test', '123', 10);
        $this->assertEquals('123', $this->cache->get('test'));
    }

    public function testCanGetCache()
    {
        $this->cache->put('test', '123', 10);
        $this->assertEquals('123', $this->cache->get('test'));
    }

    public function testCanDeleteCache()
    {
        $this->cache->put('test', '123', 10);
        $this->assertEquals('123', $this->cache->get('test'));

        $this->cache->delete('test');
        $this->assertFalse($this->cache->get('test'));
    }

    public function testCanClearCache()
    {
        $this->cache->put('test', '123', 10);
        $this->cache->put('test2', '123', 10);
        $this->cache->put('test3', '123', 10);

        $this->assertEquals('123', $this->cache->get('test'));
        $this->assertEquals('123', $this->cache->get('test2'));
        $this->assertEquals('123', $this->cache->get('test3'));

        $this->cache->clear();

        $this->assertFalse($this->cache->get('123'));
        $this->assertFalse($this->cache->get('123'));
        $this->assertFalse($this->cache->get('123'));
    }
}