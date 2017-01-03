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
