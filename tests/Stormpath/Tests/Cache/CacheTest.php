<?php namespace Stormpath\Tests\Cache;


use Stormpath\Tests\BaseTest;

class CacheTest extends BaseTest
{

    private static $application;
    private static $inited;

    protected static function init()
    {

        self::$application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache '. md5(time())));
        self::$inited = true;

    }

    public function setUp()
    {
        if (!self::$inited) {
            self::init();
        }

    }


    public static function tearDownAfterClass()
    {
        if (self::$application) {
            self::$application->delete();
        }

    }

    public function testGetFromCache()
    {

        $application = self::$application;

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache', $application->name);


        // Get the application from cache and change the name of it.
        $cache = parent::$client->dataStore->cache;
        $appInCache = $cache->get($application->href);
        $appInCache->name = 'Test';
        $cache->put($application->href, $appInCache, 10);


        // Because the app is already in cache, and we just changed the name... Lets
        // get the application again like normal and see if the name is what we set.
        $application = \Stormpath\Resource\Application::get($application->href);

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertEquals($appInCache->name, $application->name);


        // Now lets delete the cache and see if it is the orig name from the api.
        $cache->delete($application->href);


        $application = \Stormpath\Resource\Application::get($application->href);
        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache', $application->name);

    }
}