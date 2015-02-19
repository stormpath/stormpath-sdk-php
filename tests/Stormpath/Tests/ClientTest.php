<?php namespace Stormpath\Tests;


class ClientTest extends BaseTest {

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
        \Stormpath\Client::$cacheManager = 'Memcached';

        $this->assertEquals('Memcached', \Stormpath\Client::$cacheManager);
    }

    public function testCacheManagerOptionsCanBeSetStatically()
    {
        \Stormpath\Client::$cacheManagerOptions = array('item1' => true);

        $this->assertEquals(array('item1' => true), \Stormpath\Client::$cacheManagerOptions);
    }

    public function testClientInstanceDefaultsCacheIfNoCacheItemsAreSet()
    {
        $client = \Stormpath\Client::getInstance();
        $this->assertInstanceOf('Stormpath\Cache\ArrayCacheManager', $client->getCacheManager());

    }


}
