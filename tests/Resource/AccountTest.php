<?php

/*
 * Copyright 2013 Stormpath, Inc.
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
use Stormpath\Stormpath;

class AccountTest extends \Stormpath\Tests\TestCase {

    const GROUPS_COUNT = 45;

    private static $directory;
    private static $groups;
    private static $account;
    private static $inited;
    private static $application;

    protected static function init() {

        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('AccountTest Directory')));

        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                        'middleName' => 'Middle Name',
                                                                        'surname' => 'Surname',
                                                                        'username' => makeUniqueName('AccountTest') . 'username',
                                                                        'email' => makeUniqueName('AccountTest') .'@unknown123.kot',
                                                                        'password' => 'superP4ss'));

        self::$directory->createAccount(self::$account);

        self:$groups = array();

        $groupsCount = 0;
        while($groupsCount < self::GROUPS_COUNT)
        {
            $group = \Stormpath\Resource\Group::instantiate(array('name' => " $groupsCount Group Name " . phpversion(), 'description' => "The Group Description $groupsCount"));
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
        $account = \Stormpath\Resource\Account::get(self::$account->href);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        $path = \Stormpath\Resource\Account::PATH;

        //get it from id (ACCOUNT_ID)
        $accountId =  substr($account->href, strpos($account->href, $path) + strlen($path) + 1);
        $account = \Stormpath\Resource\Account::get($accountId);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        //get it from path with no slash (accounts/ACCOUNT_ID)
        $accountPath =  substr($account->href, strpos($account->href, $path));
        $account = \Stormpath\Resource\Account::get($accountPath);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        //get it from path with slash (/accounts/ACCOUNT_ID)
        $accountPath =  substr($account->href, strpos($account->href, "/$path"));
        $account = \Stormpath\Resource\Account::get($accountPath);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testGetNotFound()
    {
        \Stormpath\Resource\Account::get('unknown');
    }

    public function testGetOptions() {

        $options = array('expand' => 'groups(offset:5,limit:30)');
        $account = \Stormpath\Resource\Account::get(self::$account->href, $options);

        // testing that the groups collection was successfully expanded
        $this->assertEquals(30, count($account->groups->currentPage->items));

        //testing some expansion use cases
        $expansion = new \Stormpath\Resource\Expansion();
        $expansion->addProperty('groupMemberships', array('limit' => 2));
        $account = \Stormpath\Resource\Account::get(self::$account->href, $expansion->toExpansionArray());
        $this->assertEquals(2, $account->groupMemberships->currentPage->limit);

        $expansion->addProperty('groupMemberships', array('offset' => 1));
        $account = \Stormpath\Resource\Account::get(self::$account->href, $expansion->toExpansionArray());
        $this->assertEquals(1, $account->groupMemberships->currentPage->offset);

        $expansion->addProperty('groupMemberships', array('limit' => 10, 'offset' => 2));
        $expansion->addProperty('directory');
        $account = \Stormpath\Resource\Account::get(self::$account->href, $expansion->toExpansionArray());
        $this->assertEquals(10, $account->groupMemberships->currentPage->limit);
        $this->assertEquals(2, $account->groupMemberships->currentPage->offset);
        $this->assertEquals(3, count(array_intersect(array('name', 'description', 'status'), $account->directory->propertyNames)));

        $expansion = '?expand = directory,groupMemberships';
        $account = \Stormpath\Resource\Account::get(self::$account->href . $expansion);
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
        \Stormpath\Resource\Account::get(self::$account->href, $options);
    }

    public function testGetters()
    {
        $account = self::$account;
        $this->assertEquals('Account Name', $account->givenName);
        $this->assertEquals('Middle Name', $account->middleName);
        $this->assertEquals('Surname', $account->surname);
        $this->assertEquals('Account Name Middle Name Surname', $account->fullName);
        $this->assertEquals(\Stormpath\Stormpath::ENABLED, $account->status);
        $this->assertContains('username', $account->username);
        $this->assertContains('@unknown123.kot', $account->email);
        $this->assertInstanceOf('\Stormpath\Resource\Tenant', $account->tenant);
        $this->assertEquals(self::$client->tenant->name, $account->tenant->name);
        $this->assertInstanceOf('\Stormpath\Resource\Directory', $account->directory);
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
        $account = \Stormpath\Resource\Account::instantiate();

        $this->assertInstanceOf('\Stormpath\Resource\Account', $account);

        $account->givenName = 'Account Name';
        $account->middleName = 'Middle Name';
        $account->surname = 'Surname';
        $account->status = 'unverified';
        $account->username = 'username';
        $account->email = 'email@unknown123.kot';
        $account->password = 'superP4ssword';

        $this->assertEquals('Account Name', $account->givenName);
        $this->assertEquals('Middle Name', $account->middleName);
        $this->assertEquals('Surname', $account->surname);
        $this->assertEquals(\Stormpath\Stormpath::UNVERIFIED, $account->status);
        $this->assertEquals('username', $account->username);
        $this->assertEquals('email@unknown123.kot', $account->email);
    }

    public function testSave()
    {
        $account = self::$account;
        $account->username = 'changed_username';
        $account->email = 'changed_email@unknown123.kot';
        $account->password = 'changedPassw0rd';

        $account->save();

        $this->assertEquals('changed_username', $account->username);
        $this->assertEquals('changed_email@unknown123.kot', $account->email);
    }

    public function testApiKey()
    {
        $account = self::$account;
        $apiKey = $account->createApiKey();

        $this->assertNotEmpty($apiKey->id);
        $this->assertNotEmpty($apiKey->secret);
        $this->assertNotEmpty($apiKey->status);
        
        $this->assertEquals($account->href, $apiKey->account->href);
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
        $groups->expansion = \Stormpath\Resource\Expansion::format(array('directory'));

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

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'middleName' => 'Middle Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => makeUniqueName('AccountTest testAddGroup') . 'username',
                                                                  'email' => makeUniqueName('AccountTest testAddGroup') .'@unknown123.kot',
                                                                  'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('AccountTest testAddGroup') . "Group Name"));
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
        self::$account->addGroup(\Stormpath\Resource\Group::instantiate());
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

        $account = \Stormpath\Resource\Account::get(self::$account->href);
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

    public function testUpdatingCustomData()
    {
        $cd = self::$account->customData;

        $cd->unitTest = "some change";
        $cd->save();

        $account = \Stormpath\Resource\Account::get(self::$account->href);
        $customData = $account->customData;
        $this->assertEquals('some change', $customData->unitTest);

        // testing for issue #47
        $account = \Stormpath\Resource\Account::instantiate(array(
            'givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('AccountTest testUpdatingCustomData').'username',
            'email' => makeUniqueName('AccountTest testUpdatingCustomData').'@unknown123.kot',
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

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'middleName' => 'Middle Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => makeUniqueName('AccountTest testDelete') . 'username',
                                                                  'email' => makeUniqueName('AccountTest testDelete') .'@unknown123.kot',
                                                                  'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $href = $account->href;

        $account = \Stormpath\Resource\Account::get($href);

        // make sure the account exists before deleting
        $this->assertInstanceOf('Stormpath\Resource\Account', $account);
        $this->assertEquals('Account Name', $account->givenName);

        $account->delete();

        // should throw the expected exception after deleting
        \Stormpath\Resource\Account::get($href);
    }


    public function testShouldBeAbleToGetAccountViaHTMLFragment()
    {
        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('AccountTest testShouldBeAbleToGetAccountViaHtmlFragment') . 'username',
            'email' => makeUniqueName('AccountTest testShouldBeAbleToGetAccountViaHtmlFragment') .'@unknown123.kot',
            'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $href = $account->href;

        $hrefParts = array_reverse(explode('/',$account->href));

        $acct = \Stormpath\Resource\Account::get($hrefParts[0]);

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

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingAPasswordViaStaticCreates'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'middleName' => 'Middle Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => $username,
                                                                  'email' => makeUniqueName('AccountTest testImportingAPasswordViaStaticCreates') .'@unknown123.kot',
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

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingAPasswordViaStandardCreates'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@example.com';
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

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingSelfCreatedPasswordWithMD5'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@example.com';
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

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingSelfCreatedPasswordWithSHA512'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@example.com';
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

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingInvalidPasswordTypeShouldThrowException'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@example.com';
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

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for passwordImport' .makeUniqueName('AccountTest testImportingInvalidPasswordFormat'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
        $account->email = 'john.smith@example.com';
        $account->givenName = 'John';
        $account->password ='$2a$08$VbNS17zvQNYtMyfRiYXxWuec2F2y3SuLB/e7hU8RWdcCxxluUB3m.';
        $account->surname = 'Smith';
        $account->username = $username;


        self::$application->createAccount($account,array('passwordFormat'=>'someOtherMCF'));


        $result = self::$application->authenticate($username, 'SomePassw0rd!');
        $this->assertEquals($username, $result->account->username);

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