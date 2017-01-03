<?php
/**
 * Copyright 2017 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

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
