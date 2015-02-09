<?php

namespace Stormpath\Tests\Cache;


class MemoryCacheTest extends \PHPUnit_Framework_TestCase {

    public $cache;

    public function setUp()
    {
        $cacheManager = new \Stormpath\Cache\MemoryCacheManager(array());

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
        $this->assertNull($this->cache->get('test'));
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

        $this->assertNull($this->cache->get('123'));
        $this->assertNull($this->cache->get('123'));
        $this->assertNull($this->cache->get('123'));
    }
}