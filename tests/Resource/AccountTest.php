<?php

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
namespace Stormpath\Tests\Resource;

use Stormpath\Client;
use Stormpath\Mfa\Phone;
use Stormpath\Mfa\PhoneList;
use Stormpath\Resource\Account;
use Stormpath\Resource\Application;
use Stormpath\Resource\Directory;
use Stormpath\Resource\Expansion;
use Stormpath\Resource\Group;
use Stormpath\Resource\GroupList;
use Stormpath\Stormpath;

class AccountTest extends \Stormpath\Tests\TestCase {

    const GROUPS_COUNT = 45;

    /**
     * @var Directory
     */
    private static $directory;

    /**
     * @var GroupList
     */
    private static $groups;

    /**
     * @var Account
     */
    private static $account;

    /**
     * @var boolean
     */
    private static $inited;

    /**
     * @var Application
     */
    private static $application;

    protected static function init() {

        self::$directory = Directory::instantiate(array('name' => makeUniqueName('AccountTest Directory')));

        self::createResource(Directory::PATH, self::$directory);

        self::$account = Account::instantiate(array('givenName' => 'Account Name',
                                                                        'middleName' => 'Middle Name',
                                                                        'surname' => 'Surname',
                                                                        'username' => makeUniqueName('AccountTest') . 'username',
                                                                        'email' => makeUniqueName('AccountTest') .'@testmail.stormpath.com',
                                                                        'password' => 'superP4ss'));

        self::$directory->createAccount(self::$account);


        self:$groups = array();

        $groupsCount = 0;
        while($groupsCount < self::GROUPS_COUNT)
        {
            $group = Group::instantiate(array('name' => " $groupsCount Group Name " . phpversion(), 'description' => "The Group Description $groupsCount"));
            self::$directory->createGroup($group);
            self::$account->addGroup($group);
            $groups[$groupsCount] = $group;
            $groupsCount++;
        }

        self::$inited = true;
    }

    public function setUp()
    {
        if (!self::$inited)
        {
            self::init();
        }
    }

    public static function tearDownAfterClass()
    {
        if (self::$directory)
        {
            self::$directory->delete();
        }
        parent::tearDownAfterClass();
    }

    public function testGet() {

        // get it from full href
        $account = Account::get(self::$account->href);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        $path = Account::PATH;

        //get it from id (ACCOUNT_ID)
        $accountId =  substr($account->href, strpos($account->href, $path) + strlen($path) + 1);
        $account = Account::get($accountId);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        //get it from path with no slash (accounts/ACCOUNT_ID)
        $accountPath =  substr($account->href, strpos($account->href, $path));
        $account = Account::get($accountPath);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        //get it from path with slash (/accounts/ACCOUNT_ID)
        $accountPath =  substr($account->href, strpos($account->href, "/$path"));
        $account = Account::get($accountPath);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testGetNotFound()
    {
        Account::get('unknown');
    }

    public function testGetOptions() {

        $options = array('expand' => 'groups(offset:5,limit:30)');
        $account = Account::get(self::$account->href, $options);

        // testing that the groups collection was successfully expanded
        $this->assertEquals(30, count($account->groups->currentPage->items));

        //testing some expansion use cases
        $expansion = new Expansion();
        $expansion->addProperty('groupMemberships', array('limit' => 2));
        $account = Account::get(self::$account->href, $expansion->toExpansionArray());
        $this->assertEquals(2, $account->groupMemberships->currentPage->limit);

        $expansion->addProperty('groupMemberships', array('offset' => 1));
        $account = Account::get(self::$account->href, $expansion->toExpansionArray());
        $this->assertEquals(1, $account->groupMemberships->currentPage->offset);

        $expansion->addProperty('groupMemberships', array('limit' => 10, 'offset' => 2));
        $expansion->addProperty('directory');
        $account = Account::get(self::$account->href, $expansion->toExpansionArray());
        $this->assertEquals(10, $account->groupMemberships->currentPage->limit);
        $this->assertEquals(2, $account->groupMemberships->currentPage->offset);
        $this->assertEquals(3, count(array_intersect(array('name', 'description', 'status'), $account->directory->propertyNames)));

        $expansion = '?expand = directory,groupMemberships';
        $account = Account::get(self::$account->href . $expansion);
        $this->assertEquals(25, $account->groupMemberships->currentPage->limit);
        $this->assertEquals(0, $account->groupMemberships->currentPage->offset);
        $this->assertEquals(3, count(array_intersect(array('name', 'description', 'status'), $account->directory->propertyNames)));
    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testGetWithBadOptions() {

        // bad expansion format
        $options = array('expand' => 'groups(5,30)');
        Account::get(self::$account->href, $options);
    }

    public function testGetters()
    {
        $account = self::$account;
        $this->assertEquals('Account Name', $account->givenName);
        $this->assertEquals('Middle Name', $account->middleName);
        $this->assertEquals('Surname', $account->surname);
        $this->assertEquals('Account Name Middle Name Surname', $account->fullName);
        $this->assertEquals(Stormpath::ENABLED, $account->status);
        $this->assertContains('username', $account->username);
        $this->assertContains('@testmail.stormpath.com', $account->email);
        $this->assertInstanceOf('\Stormpath\Resource\Tenant', $account->tenant);
        $this->assertEquals(self::$client->tenant->name, $account->tenant->name);
        $this->assertInstanceOf('\Stormpath\Resource\Directory', $account->directory);
        $this->assertInstanceOf(\Stormpath\Mfa\PhoneList::class, $account->phones);
        $this->assertInstanceOf(\Stormpath\Mfa\FactorList::class, $account->factors);
        $this->assertEquals(self::$directory->name, $account->directory->name);
        $account->emailVerificationToken;

        $groupsCount = 0;
        foreach($account->groups as $group)
        {
            $this->assertInstanceOf('\Stormpath\Resource\Group', $group);
            $groupsCount++;
        }

        $this->assertEquals(self::GROUPS_COUNT, $groupsCount);

        $groupsCount = 0;
        foreach($account->groupMemberships as $groupMembership)
        {
            $this->assertInstanceOf('\Stormpath\Resource\GroupMembership', $groupMembership);
            $groupsCount++;
        }

        $this->assertEquals(self::GROUPS_COUNT, $groupsCount);

    }

    public function testSetters()
    {
        $account = Account::instantiate();

        $this->assertInstanceOf('\Stormpath\Resource\Account', $account);

        $account->givenName = 'Account Name';
        $account->middleName = 'Middle Name';
        $account->surname = 'Surname';
        $account->status = 'unverified';
        $account->username = 'username';
        $account->email = 'email@testmail.stormpath.com';
        $account->password = 'superP4ssword';

        $this->assertEquals('Account Name', $account->givenName);
        $this->assertEquals('Middle Name', $account->middleName);
        $this->assertEquals('Surname', $account->surname);
        $this->assertEquals(Stormpath::UNVERIFIED, $account->status);
        $this->assertEquals('username', $account->username);
        $this->assertEquals('email@testmail.stormpath.com', $account->email);
    }

    public function testSave()
    {
        try {
            $account = self::$account;
            $account->username = 'changed_username';
            $account->email = 'changed_email@testmail.stormpath.com';
            $account->password = 'changedPassw0rd';

            $account->save();

            $this->assertEquals('changed_username', $account->username);
            $this->assertEquals('changed_email@testmail.stormpath.com', $account->email);
        } catch(\Exception $e) {
            var_dump($e);
        }
    }

    public function testApiKey()
    {
        $account = self::$account;
        $apiKey = $account->createApiKey([
            'name' => 'Test Api Key',
            'description' => 'Description'
        ]);

        $this->assertNotEmpty($apiKey->id);
        $this->assertNotEmpty($apiKey->secret);
        $this->assertNotEmpty($apiKey->status);
        $this->assertEquals('Test Api Key', $apiKey->getName());
        $this->assertEquals('Description', $apiKey->getDescription());


        $apiKey->setName('Name');
        $apiKey->setDescription('Desc');

        $this->assertEquals('Name', $apiKey->getName());
        $this->assertEquals('Desc', $apiKey->getDescription());

        $this->assertEquals($account->href, $apiKey->account->href);
        $this->assertContains('/tenants/', $apiKey->tenant->href);

        $apiKey2 = $account->createApiKey();
        $this->assertNull($apiKey2->getName());
        $this->assertNull($apiKey2->getDescription());
    }


    public function testGroupsOptions()
    {
        $account = self::$account;

        $options = array('offset' => 1, 'limit' => 2, 'orderBy' => 'name desc', 'q' => '9', 'expand' => 'directory');
        $groups = $account->getGroups($options);

        $this->assertGroupOptions($groups);
    }

    public function testGroupsCollectionOptions()
    {
        $account = self::$account;

        $groups = $account->getGroups();
        $groups->offset = 1;
        $groups->limit = 2;
        $groups->order = \Stormpath\Resource\Order::format(array('name'), 'desc');
        $search = new \Stormpath\Resource\Search();
        $groups->search = $search->setFilter(9);
        $groups->expansion = Expansion::format(array('directory'));

        $this->assertGroupOptions($groups);

        $groups->search = 'q=9';
        $groups->order = 'name desc';
        $groups->expansion = array('directory');

        $this->assertGroupOptions($groups);

        $groups->search = array('q' => 9);
        $groups->expansion = 'directory';

        $this->assertGroupOptions($groups);

        $order = new \Stormpath\Resource\Order(array('name'), 'd');
        $groups->order = strval($order);

        $search = new \Stormpath\Resource\Search();
        $groups->search = $search->addStartsWith('description', 'group description')->
                                    addEndsWith('name', 'name')->
                                    addEquals('status', 'enabled');

        $this->assertGroupOptions($groups);

        $groups->search = $search->addMatchAnywhere('name', 9);

        $this->assertGroupOptions($groups);
    }

    /*
     * This function expects the following criteria:
     * offset=1 & limit=2 & orderBy=name desc & q=9 & expand=directory
     * or equivalent.
     */
    private function assertGroupOptions($groups)
    {
        $groupsCount = 0;
        foreach($groups as $group)
        {
            $this->assertInstanceOf('\Stormpath\Resource\Group', $group);
            $groupsCount++;

            // testing the expansion
            $this->assertTrue(count(array_intersect(array('name', 'description', 'status'), $group->directory->propertyNames)) == 3);

            // testing the order (desc) and pagination of the collection
            if ($groupsCount == 1)
            {
                $this->assertContains('39 Group Name', $group->name);
            }

            if ($groupsCount == 2)
            {
                $this->assertContains('29 Group Name', $group->name);
            }

            if ($groupsCount == 3)
            {
                $this->assertContains('19 Group Name', $group->name);
            }

            // we should receive 4 groups that match the filter q=9,
            // but we set the offset at 1 so the total items we'll get should be 3.
            $this->assertTrue($groupsCount <= 3);
        }
    }

    public function testAddGroup()
    {

        $account = Account::instantiate(array('givenName' => 'Account Name',
                                                                  'middleName' => 'Middle Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => makeUniqueName('AccountTest testAddGroup') . 'username',
                                                                  'email' => makeUniqueName('AccountTest testAddGroup') .'@testmail.stormpath.com',
                                                                  'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $group = Group::instantiate(array('name' => makeUniqueName('AccountTest testAddGroup') . "Group Name"));
        self::$directory->createGroup($group);

        $account->addGroup($group);

        $this->assertEquals(1, count($account->groups->currentPage->items));
        foreach($account->groups as $grp)
        {
            $this->assertInstanceOf('\Stormpath\Resource\Group', $grp);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddGroupNonExistent()
    {
        self::$account->addGroup(Group::instantiate());
    }


    public function testItCanGetAccessTokensOffAccount()
    {
        $tokens = self::$account->accessTokens;
        $this->assertInstanceOf('Stormpath\Resource\AccessTokenList', $tokens);
    }

    public function testItCanGetRefreshTokensOffAccount()
    {
        $tokens = self::$account->refreshTokens;
        $this->assertInstanceOf('Stormpath\Resource\RefreshTokenList', $tokens);
    }


	public function testAddingCustomData()
	{
		$cd = self::$account->customData;

		$cd->unitTest = "unit Test";
		$cd->save();

		$account = Account::get(self::$account->href);
		$customData = $account->customData;
		$this->assertEquals('unit Test', $customData->unitTest);

		$customData = self::$account->customData;
		$customData->locations = array('BuildingA', 'BuildingB');
		$customData->save();

		$this->assertEquals(array('BuildingA', 'BuildingB'), $customData->locations);

		$customData->locations = array('BuildingA', 'BuildingB', 'BuildingC');
		$customData->save();

		$newClient = self::newClientInstance();
		$customData = $newClient->getDataStore()->getResource($customData->href, Stormpath::CUSTOM_DATA);
		$this->assertEquals(array('BuildingA', 'BuildingB', 'BuildingC'), $customData->locations);
	}

//	public function testCustomDataSearch()
//	{
//
//
//		$account = Account::instantiate(array('givenName' => 'Account Name',
//			'middleName' => 'Middle Name',
//			'surname' => 'Surname',
//			'username' => makeUniqueName('AccountTest') . 'username',
//			'email' => makeUniqueName('AccountTest') .'@testmail.stormpath.com',
//			'password' => 'superP4ss'));
//
//		self::$directory->createAccount($account);
//
//		$time = microtime();
//		$cd = $account->customData;
//		$cd->unitTest = $time;
//		$cd->save();
//
//
//		$client = Client::getInstance();
//
//		$accounts = $client->tenant->accounts->setSearch(['customData.unitTest' => $time]);
//		if($accounts->size == 1) {
//			$this->assertEquals(1, $accounts->size);
//		} else {
//			$this->markTestSkipped('Could not find account with custom data, Possibly not indexed in time.');
//		}
//
//	}

    public function testUpdatingCustomData()
    {
        $cd = self::$account->customData;

        $cd->unitTest = "some change";
        $cd->save();

        $account = Account::get(self::$account->href);
        $customData = $account->customData;
        $this->assertEquals('some change', $customData->unitTest);

        // testing for issue #47
        $account = Account::instantiate(array(
            'givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('AccountTest testUpdatingCustomData').'username',
            'email' => makeUniqueName('AccountTest testUpdatingCustomData').'@testmail.stormpath.com',
            'password' => '123quEso'));
        self::$directory->createAccount($account);

        $account->middleName = 'Test middle name';
        $customData = $account->customData;
        $customData->phoneNumber = '123-456789';
        $account->save();

        $customData = $account->customData;
        $customData->companyName = 'Company Test';
        $account->save();

        $newClient = self::newClientInstance();
        $account = $newClient->dataStore->getResource($account->href, Stormpath::ACCOUNT);
        $customData = $account->customData;
        $this->assertEquals('Test middle name', $account->middleName);
        $this->assertEquals('Company Test', $customData->companyName);
        $this->assertEquals('123-456789', $customData->phoneNumber);

        $account->delete();
    }

    public function testRemovingCustomData()
    {
        $cd = self::$account->customData;

        $cd->remove('unitTest');

        $newClient = self::newClientInstance();
        $account = $newClient->dataStore->getResource(self::$account->href, Stormpath::ACCOUNT);
        $customData = $account->customData;
        $this->assertNull($customData->unitTest);
    }

    public function testDeletingAllCustomData()
    {
        $cd = self::$account->customData;
        $cd->unitTest = "some change";
        $cd->rank = "Captain";
        $cd->birthDate = "2305-07-13";
        $cd->favoriteDrink = "favoriteDrink";
        $cd->save();

        $cd->delete();

        $newClient = self::newClientInstance();
        $account = $newClient->dataStore->getResource(self::$account->href, Stormpath::ACCOUNT);
        $customData = $account->customData;
        $this->assertNull($customData->unitTest);
        $this->assertNull($customData->rank);
        $this->assertNull($customData->birthDate);
        $this->assertNull($customData->favoriteDrink);
    }






    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testDelete()
    {

        $account = Account::instantiate(array('givenName' => 'Account Name',
                                                                  'middleName' => 'Middle Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => makeUniqueName('AccountTest testDelete') . 'username',
                                                                  'email' => makeUniqueName('AccountTest testDelete') .'@testmail.stormpath.com',
                                                                  'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $href = $account->href;

        $account = Account::get($href);

        // make sure the account exists before deleting
        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        $account->delete();

        // should throw the expected exception after deleting
        Account::get($href);
    }


    public function testShouldBeAbleToGetAccountViaHTMLFragment()
    {
        $account = Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('AccountTest testShouldBeAbleToGetAccountViaHtmlFragment') . 'username',
            'email' => makeUniqueName('AccountTest testShouldBeAbleToGetAccountViaHtmlFragment') .'@testmail.stormpath.com',
            'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $href = $account->href;

        $hrefParts = array_reverse(explode('/',$account->href));

        $acct = Account::get($hrefParts[0]);

        $this->assertInstanceOf('\Stormpath\Resource\Account', $account);
        $this->assertEquals($href, $acct->href);

        $acct2 = \Stormpath\Client::get($hrefParts[1].'/'.$hrefParts[0], Stormpath::ACCOUNT);

        $this->assertInstanceOf('\Stormpath\Resource\Account', $acct2);
        $this->assertEquals($href, $acct2->href);

        $account->delete();


    }

    public function testImportingAPasswordViaStaticCreates()
    {
        // SomePassw0rd!
        $username = makeUniqueName('AccountTest testImportingAPasswordViaStaticCreates') . 'username';

        self::$application = Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingAPasswordViaStaticCreates'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(Application::PATH, self::$application, array('createDirectory' => true));

        $account = Account::instantiate(array('givenName' => 'Account Name',
                                                                  'middleName' => 'Middle Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => $username,
                                                                  'email' => makeUniqueName('AccountTest testImportingAPasswordViaStaticCreates') .'@testmail.stormpath.com',
                                                                  'password' => '$2a$08$VbNS17zvQNYtMyfRiYXxWuec2F2y3SuLB/e7hU8RWdcCxxluUB3m.'));

        self::$application->createAccount($account, array('passwordFormat'=>'mcf'));


        $result = self::$application->authenticate($username, 'SomePassw0rd!');
        $this->assertEquals($username, $result->account->username);

        $account->delete();

    }

    public function testImportingAPasswordViaStandardCreate()
    {
        // SomePassw0rd!
        $username = makeUniqueName('AccountTest testImportingAPasswordViaStandardCreates') . 'username';
        $client = Client::getInstance();

        self::$application = Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingAPasswordViaStandardCreates'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@testmail.stormpath.com';
        $account->givenName = 'John';
        $account->password ='$2a$08$VbNS17zvQNYtMyfRiYXxWuec2F2y3SuLB/e7hU8RWdcCxxluUB3m.';
        $account->surname = 'Smith';
        $account->username = $username;


        self::$application->createAccount($account,array('passwordFormat'=>'mcf'));


        $result = self::$application->authenticate($username, 'SomePassw0rd!');
        $this->assertEquals($username, $result->account->username);

        $account->delete();

    }

    public function testImportingSelfCreatedPasswordWithMD5()
    {

        $username = makeUniqueName('AccountTest testImportingSelfCreatedPasswordWithMD5') . 'username';
        $client = Client::getInstance();

        self::$application = Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingSelfCreatedPasswordWithMD5'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@testmail.stormpath.com';
        $account->givenName = 'John';
        $account->password = '$stormpath2$MD5$1$NzEyN2ZhYzdkZTAyMjJlMGQyMWYxMWRmZmY2YjA1MWI=$K18Ak0YikAFrqgglhIaY5g==';
        $account->surname = 'Smith';
        $account->username = $username;


        self::$application->createAccount($account,array('passwordFormat'=>'mcf'));


        $result = self::$application->authenticate($username, 'password');
        $this->assertEquals($username, $result->account->username);

        $account->delete();
    }

    public function testImportingSelfCreatedPasswordWithSHA512()
    {

        $username = makeUniqueName('AccountTest testImportingSelfCreatedPasswordWithSHA512') . 'username';
        $client = Client::getInstance();

        self::$application = Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingSelfCreatedPasswordWithSHA512'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@testmail.stormpath.com';
        $account->givenName = 'John';
        $account->password = '$stormpath2$SHA-512$1$ZFhBRmpFSnEwVEx2ekhKS0JTMDJBNTNmcg==$Q+sGFg9e+pe9QsUdfnbJUMDtrQNf27ezTnnGllBVkQpMRc9bqH6WkyE3y0svD/7cBk8uJW9Wb3dolWwDtDLFjg==';
        $account->surname = 'Smith';
        $account->username = $username;


        self::$application->createAccount($account,array('passwordFormat'=>'mcf'));


        $result = self::$application->authenticate($username, 'testing12');
        $this->assertEquals($username, $result->account->username);

        $account->delete();
    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     * @expectedExceptionMessage The imported password is in an invalid format
     */
    public function testImportingInvalidPasswordTypeShouldThrowException()
    {

        $username = makeUniqueName('AccountTest testImportingInvalidPasswordTypeShouldThrowException') . 'username';
        $client = Client::getInstance();

        self::$application = Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingInvalidPasswordTypeShouldThrowException'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@testmail.stormpath.com';
        $account->givenName = 'John';
        $account->password ='$INVALID$04$RZPSLGUz3dRdm7aRfxOeYuKeueSPW2YaTpRkszAA31wcPpyg6zkGy';
        $account->surname = 'Smith';
        $account->username = $username;


        self::$application->createAccount($account,array('passwordFormat'=>'mcf'));


        $result = self::$application->authenticate($username, 'SomePassw0rd!');
        $this->assertEquals($username, $result->account->username);

        $account->delete();
    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     * @expectedExceptionMessage The imported password designates an algorithm that is an unsupported value.
     */
    public function testImportingInvalidPasswordFormatTypeShouldThrowException()
    {

        $username = makeUniqueName('AccountTest testImportingInvalidPasswordFormat') . 'username';
        $client = Client::getInstance();

        self::$application = Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingInvalidPasswordFormat'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@testmail.stormpath.com';
        $account->givenName = 'John';
        $account->password ='$2a$08$VbNS17zvQNYtMyfRiYXxWuec2F2y3SuLB/e7hU8RWdcCxxluUB3m.';
        $account->surname = 'Smith';
        $account->username = $username;


        self::$application->createAccount($account,array('passwordFormat'=>'someOtherMCF'));


        $result = self::$application->authenticate($username, 'SomePassw0rd!');
        $this->assertEquals($username, $result->account->username);

        $account->delete();
    }

    /** @test */
    public function a_password_modified_at_date_is_available()
    {
        $account = Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('AccountTest testAddGroup') . 'username',
            'email' => makeUniqueName('AccountTest testAddGroup') .'@testmail.stormpath.com',
            'password' => 'superP4ss'));

        $account = self::$directory->createAccount($account);

        $account->password = 'superP4ss!';
        $account->save();

        $this->assertNotNull($account->passwordModifedAt);

        $account->delete();
    }

    /** @test */
    public function a_phone_can_be_added_to_an_account()
    {
        $account = Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('AccountTest phone') . 'username',
            'email' => makeUniqueName('AccountTest phone') .'@testmail.stormpath.com',
            'password' => 'superP4ss'));

        $account = self::$directory->createAccount($account);

        $phone = Phone::instantiate([
            'name' => 'test phone number',
            'description' => 'Test Phone',
            'number' => '(888) 391-5282'
        ]);

        /** @var PhoneList $phoneList */
        $phoneList = $account->getPhones();

        $this->assertEquals(0, $phoneList->getSize(), 'The account already has phones');

        $addedPhone = $account->addPhone($phone);

        $phoneList = $account->getPhones();

        $this->assertEquals(1, $phoneList->getSize(), 'The account does not contain exactly 1 phone');

        $this->assertInstanceOf(
            Stormpath::PHONE,
            $addedPhone,
            'Adding a phone to an account did not return a phone object'
        );

        $account->delete();
    }

    /** @test */
    public function a_phone_can_be_removed_from_an_account()
    {
        $account = Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('AccountTest phone') . 'username',
            'email' => makeUniqueName('AccountTest phone') .'@testmail.stormpath.com',
            'password' => 'superP4ss'));

        $account = self::$directory->createAccount($account);

        $phone = Phone::instantiate([
            'name' => 'test phone number',
            'description' => 'Test Phone',
            'number' => '(888) 391-5282'
        ]);

        /** @var PhoneList $phoneList */
        $phoneList = $account->getPhones();

        $this->assertEquals(0, $phoneList->getSize(), 'The account already has phones');

        $addedPhone = $account->addPhone($phone);

        $phoneList = $account->getPhones();

        $this->assertEquals(1, $phoneList->getSize(), 'The account does not contain exactly 1 phone');

        $addedPhone->delete();

        $phoneList = $account->getPhones();

        $this->assertEquals(0, $phoneList->getSize(), 'The account has 1 or more phones');

        $account->delete();
    }
    


    public function tearDown()
    {
        if (self::$application)
        {
            $accountStoreMappings = self::$application->accountStoreMappings;

            if ($accountStoreMappings)
            {
                foreach($accountStoreMappings as $asm)
                {
                    $accountStore = $asm->accountStore;
                    $asm->delete();
                    $accountStore->delete();
                }
            }

            self::$application->delete();
        }
        self::$application = null;
    }

}