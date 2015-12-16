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


use Stormpath\Stormpath;

class DirectoryTest extends \Stormpath\Tests\TestCase {

    private static $directory;
    private static $inited;

    protected static function init()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('DirectoryTest'), 'description' => 'Main Directory description'));
        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);
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

    public function testGet()
    {
        $directory = \Stormpath\Resource\Directory::get(self::$directory->href);

        $this->assertInstanceOf('\Stormpath\Resource\Directory', $directory);
        $this->assertContains('DirectoryTest', $directory->name);
        $this->assertContains('Main Directory description', $directory->description);
        $this->assertInstanceOf('\Stormpath\Resource\GroupList', $directory->groups);
        $this->assertInstanceOf('\Stormpath\Resource\AccountList', $directory->accounts);
        $this->assertInstanceOf('\Stormpath\Resource\Tenant', $directory->tenant);
        $this->assertEquals(self::$client->tenant->name, $directory->tenant->name);
    }

    public function testCreate()
    {
        $directory = \Stormpath\Resource\Directory::create(array('name' => makeUniqueName('DirectoryTest testCreate'), 'description' => 'A Random Directory description', 'status' => 'disabled'));

        $this->assertInstanceOf('\Stormpath\Resource\Directory', $directory);
        $this->assertContains('testCreate', $directory->name);
        $this->assertEquals('A Random Directory description', $directory->description);
        $this->assertEquals('DISABLED', $directory->status);

        $directory->delete();
    }

    public function testSave()
    {
        $directory = self::$directory;

        $directory->name = makeUniqueName('testSave Main Directory');
        $directory->status = 'disabled';
        $directory->description = 'Main Directory description changed';
        $directory->save();

        $directory = \Stormpath\Resource\Directory::get(self::$directory->href);
        $this->assertContains('testSave', $directory->name);
        $this->assertContains('Main Directory description changed', $directory->description);
        $this->assertEquals('DISABLED', $directory->status);
    }

    public function testCreateAccount()
    {
        $directory = self::$directory;
        $directory->status = 'enabled';
        $directory->save();

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                   'surname' => 'Surname',
                                                                   'email' => makeUniqueName('DirectoryTest createAccount') . '@unknown123.kot',
                                                                   'password' => 'superP4ss'));

        $directory->createAccount($account, array('registrationWorkflowEnabled' => false));

        $account = \Stormpath\Resource\Account::get($account->href);

        $this->assertContains('Main_Directory', $account->directory->name);
        $this->assertEquals('Account Name', $account->givenName);

        $account->delete();
    }

    public function testCreateGroup()
    {
        $directory = self::$directory;
        $directory->status = 'enabled';
        $directory->save();

        $group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('DirectoryTest createGroup')));

        $directory->createGroup($group);

        $group = \Stormpath\Resource\Group::get($group->href);

        $this->assertContains('Main_Directory', $group->directory->name);
        $this->assertContains('createGroup', $group->name);

        $group->delete();
    }

    public function testAddingCustomData()
    {
        $cd = self::$directory->customData;

        $cd->unitTest = "unit Test";
        $cd->save();

        $directory = \Stormpath\Resource\Directory::get(self::$directory->href);
        $customData = $directory->customData;
        $this->assertEquals('unit Test', $customData->unitTest);



    }

    public function testUpdatingCustomData()
    {
        $cd = self::$directory->customData;

        $cd->unitTest = "some change";
        $cd->save();

        $directory = \Stormpath\Resource\Directory::get(self::$directory->href);
        $customData = $directory->customData;
        $this->assertEquals('some change', $customData->unitTest);

        // testing for issue #47
        $directory = \Stormpath\Resource\Directory::instantiate(array(
            'name' => makeUniqueName('DirectoryTest updatingCustomData'),
            'description' => 'Test Directory description'));
        self::createResource(\Stormpath\Resource\Directory::PATH, $directory);

        $directory->description = 'Test description';
        $customData = $directory->customData;
        $customData->companyName = 'Company Test';
        $directory->save();

        $newClient = self::newClientInstance();
        $directory = $newClient->dataStore->getResource($directory->href, Stormpath::DIRECTORY);
        $this->assertEquals('Test description', $directory->description);
        $this->assertEquals('Company Test', $directory->customData->companyName);

        $directory->delete();

    }

    public function testRemovingCustomData()
    {
        $cd = self::$directory->customData;

        $cd->remove('unitTest');

        $newClient = self::newClientInstance();
        $directory = $newClient->dataStore->getResource(self::$directory->href, Stormpath::DIRECTORY);
        $customData = $directory->customData;
        $this->assertNull($customData->unitTest);
    }

    public function testDeletingAllCustomData()
    {
        $cd = self::$directory->customData;
        $cd->unitTest = "some change";
        $cd->rank = "Captain";
        $cd->birthDate = "2305-07-13";
        $cd->favoriteDrink = "favoriteDrink";
        $cd->save();

        $cd->delete();

        $newClient = self::newClientInstance();
        $directory = $newClient->dataStore->getResource(self::$directory->href, Stormpath::DIRECTORY);
        $customData = $directory->customData;
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
        $directory = \Stormpath\Resource\Directory::create(array('name' => makeUniqueName('DirectoryTest testDelete')));

        $this->assertInstanceOf('\Stormpath\Resource\Directory', $directory);
        $this->assertContains('testDelete', $directory->name);

        $href = $directory->href;

        $directory->delete();

        \Stormpath\Resource\Directory::get($href);
    }

    public function testShouldBeAbleToGetDirectoryViaHTMLFragment()
    {
        $directory = \Stormpath\Resource\Directory::create(array('name' => makeUniqueName('DirectoryTest htmlFragment')));

        $href = $directory->href;

        $hrefParts = array_reverse(explode('/',$href));

        $dir = \Stormpath\Resource\Directory::get($hrefParts[0]);

        $this->assertInstanceOf('\Stormpath\Resource\Directory', $dir);
        $this->assertEquals($href, $dir->href);

        $dir2 = \Stormpath\Client::get($hrefParts[1].'/'.$hrefParts[0], Stormpath::DIRECTORY);

        $this->assertInstanceOf('\Stormpath\Resource\Directory', $dir2);
        $this->assertEquals($href, $dir2->href);

        $directory->delete();


    }

}