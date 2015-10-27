<?php namespace Stormpath\Tests\Cache;


use Stormpath\Cache\Cacheable;
use Stormpath\Tests\BaseTest;

class CacheTest extends BaseTest
{

    private static $application;
    private static $inited;

    protected static function init()
    {

        self::$application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache '. md5(time().microtime().uniqid())));
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
        parent::tearDownAfterClass();

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

    public function testDeletesFromCacheWhenResourceIsDeleted()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache Delete '. md5(time().microtime().uniqid())));
        $cache = parent::$client->dataStore->cache;

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache Delete', $application->name);

        $application->delete();

        $appInCache = $cache->get($application->href);

        $this->assertNull($appInCache);
    }

    public function testWillUpdateCacheWhenResourceUpdates()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Cache Update '. md5(time().microtime().uniqid())));
        $cache = parent::$client->dataStore->cache;

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App for Cache Update', $application->name);

        $application->name = 'Test Update '. md5(time().microtime().uniqid());
        $application->save();

        $appInCache = $cache->get($application->href);


        $this->assertContains('Test Update', $appInCache->name);
        $application->delete();
    }

    public function testDoesNotCacheExpandedResource()
    {
        $cache = parent::$client->dataStore->cache;
        // Create Account and add to Group
        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => 'Main Directory' .md5(time())));
        self::createResource(\Stormpath\Resource\Directory::PATH, $directory);

        $group = \Stormpath\Resource\Group::instantiate(array('name' => 'A New Group' . md5(time())));

        $directory->createGroup($group);

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'surname' => 'Surname',
            'email' => md5(time()) .'@unknown12345.kot',
            'password' => 'superP4ss'));

        $directory->createAccount($account);

        $groupMembership = \Stormpath\Resource\GroupMembership::instantiate();
        $groupMembership->account = $account;
        $groupMembership->group = $group;
        $groupMembership = \Stormpath\Resource\GroupMembership::create($groupMembership);
        // Check the cache
        $acct = \Stormpath\Resource\Account::get($account->href,array('expand'=>'groups,groupMemberships'));
        $this->assertEquals($acct->surname, 'Surname', 'one');

        // Make change to the account
        $account->surname = "Test";
        $account->save();
        $acct2 = \Stormpath\Resource\Account::get($account->href,array('expand'=>'groups,groupMemberships'));

        $this->assertNull($cache->get($acct->href.":groups,groupMemberships"));
        $this->assertNull($cache->get($acct2->href.":groups,groupMemberships"));

        $directory->delete();
    }

    public function testNullCacheDoesNotCache()
    {
        $origClient = parent::$client->dataStore->cache;
        parent::$client->tearDown();
        \Stormpath\Client::$cacheManager = 'Null';

        $client = \Stormpath\Client::getInstance();
        $cache = $client->cacheManager->getCache();

        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App for Null Cache '. md5(time().microtime().uniqid())));

        $appInCache = $cache->get($application->href);

        $this->assertNull($appInCache);

        $application->delete();


        parent::$client = $origClient;
    }

    public function testWillNotCacheHrefOnlyObject()
    {
        $object = new \stdClass();

        $object->href = "http://api.stormpath.com/v1/account/123abc";

        $cachable = new CacheTestingMock();

        $isCacheable = $cachable->isCachable($object);

        $this->assertFalse($isCacheable);
    }


}

class CacheTestingMock extends Cacheable
{
    public function isCachable($resource)
    {
        return $this->resourceIsCacheable($resource);
    }
}