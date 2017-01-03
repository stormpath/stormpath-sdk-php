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

use Stormpath\Cache\Cacheable;
use Stormpath\Cache\PSR6CacheKeyTrait;
use Stormpath\Client;
use Stormpath\Tests\TestCase;

class CacheTest extends TestCase
{
    use PSR6CacheKeyTrait;

    public function testGetFromCache()
    {

        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache Get '. md5(time().microtime().uniqid())));

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache Get', $application->name);


        // Get the application from cache and change the name of it.
        $cachePool = Client::getInstance()->getCachePool();
        $item = $cachePool->getItem($this->createCacheKey($application->href));
        $this->assertTrue($item->isHit(), 'Application not found in cache');
        $appInCache = $item->get();
        $appInCache->name = 'Test';
        $item->set($appInCache);
        $item->expiresAfter(60);
        $cachePool->save($item);


        // Because the app is already in cache, and we just changed the name... Lets
        // get the application again like normal and see if the name is what we set.
        $application = \Stormpath\Resource\Application::get($application->href);

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertEquals($appInCache->name, $application->name);


        // Now lets delete the cache and see if it is the orig name from the api.
        $cachePool->deleteItem($this->createCacheKey($application->href));


        $application = \Stormpath\Resource\Application::get($application->href);
        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache Get', $application->name);

        $application->delete();
    }

    public function testDeletesFromCacheWhenResourceIsDeleted()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache Delete '. md5(time().microtime().uniqid())));
        $cachePool = Client::getInstance()->getCachePool();

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache Delete', $application->name);

        $application->delete();

        $item = $cachePool->getItem($this->createCacheKey($application->href));

        $this->assertFalse($item->isHit());
    }

    public function testWillUpdateCacheWhenResourceUpdates()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache Update '. md5(time().microtime().uniqid())));
        $cachePool = Client::getInstance()->getCachePool();

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache Update', $application->name);

        $application->name = 'Test Update '. md5(time().microtime().uniqid());
        $application->save();

        $item = $cachePool->getItem($this->createCacheKey($application->href));
        $this->assertTrue($item->isHit(), 'Application not found in cache');
        $appInCache = $item->get();


        $this->assertContains('Test Update', $appInCache->name);
        $application->delete();
    }

    public function testNullCacheDoesNotCache()
    {
        $origClient = parent::$client;
        parent::$client->tearDown();
        \Stormpath\Client::$cacheManager = 'Null';

        $client = \Stormpath\Client::getInstance();
        $cachePool = $client->getCachePool();

        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Null Cache '. md5(time().microtime().uniqid())));

        $item = $cachePool->getItem($this->createCacheKey($application->href));

        $this->assertFalse($item->isHit());

        $application->delete();


        parent::$client = $origClient;
        parent::$client->tearDown();
        \Stormpath\Client::$cacheManager = 'Array';
    }

    public function testWillNotCacheHrefOnlyObject()
    {
        $object = new \stdClass();

        $object->href = "http://api.stormpath.com/v1/account/123abc";

        $cachable = new CacheTestingMock();

        $isCacheable = $cachable->isCachable($object);

        $this->assertFalse($isCacheable);
    }

    public function testWillInvalidateExpands()
    {
        $cachePool = Client::getInstance()->getCachePool();

        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache Expand tagging '. md5(time().microtime().uniqid())));

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache Expand tagging', $application->name);

        $item = $cachePool->getItem($this->createCacheKey($application->href));
        $this->assertTrue($item->isHit());

        $expandedApplication = Client::getInstance()
            ->getDataStore()
            ->getResource($application->href, \Stormpath\Stormpath::APPLICATION, array('expand' => 'customData'));

        $item = $cachePool->getItem($this->createCacheKey($application->href, array('expand' => 'customData')));
        $this->assertTrue($item->isHit());

        $customData = $expandedApplication->getCustomData();
        $customData->foo = 'bar';
        $customData->save();

        $item = $cachePool->getItem($this->createCacheKey($application->href, array('expand' => 'customData')));
        $this->assertFalse($item->isHit());

        $application->delete();
    }
}

class CacheTestingMock extends Cacheable
{
    public function isCachable($resource)
    {
        return $this->resourceIsCacheable($resource);
    }
}
