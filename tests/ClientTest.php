<?php namespace Stormpath\Tests;
/*
 * Copyright 2016 Stormpath, Inc.
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
 */


class ClientTest extends TestCase {

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoApiKeyFile()
    {
        $builder = new \Stormpath\ClientBuilder();
        $builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidApiKeyId()
    {
        $builder = new \Stormpath\ClientBuilder();
        $builder->
        setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
        setApiKeyIdPropertyName('badId')->
        build();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidApiKeySecret()
    {
        $builder = new \Stormpath\ClientBuilder();
        $builder->
        setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
        setApiKeySecretPropertyName('badSecret')->
        build();
    }

    public function testClientFromPropertiesString()
    {
        $builder = new \Stormpath\ClientBuilder();
        $result = $builder->
        setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
        setApiKeyProperties("apiKey.id=something\napiKey.secret=somethingSecret")->
        build();

        $this->assertInstanceOf('Stormpath\Client', $result);
    }

    public function testCacheManagerCanBeSetStatically()
    {
        $origCacheManager =  \Stormpath\Client::$cacheManager;
        \Stormpath\Client::$cacheManager = 'Memcached';

        $this->assertEquals('Memcached', \Stormpath\Client::$cacheManager);
        \Stormpath\Client::$cacheManager = $origCacheManager;
    }

    public function testCacheManagerOptionsCanBeSetStatically()
    {
        \Stormpath\Client::$cacheManagerOptions = array('item1' => true);

        $this->assertEquals(array('item1' => true), \Stormpath\Client::$cacheManagerOptions);
    }

    public function testClientInstanceDefaultsCacheIfNoCacheItemsAreSet()
    {
        \Stormpath\Client::tearDown();
        $client = \Stormpath\Client::getInstance();
        $this->assertInstanceOf('Cache\Taggable\TaggablePoolInterface', $client->getCachePool());

    }


}
