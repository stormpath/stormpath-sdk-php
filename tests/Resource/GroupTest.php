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


use Stormpath\Stormpath;

class GroupTest extends \Stormpath\Tests\TestCase {

    private static $directory;
    private static $group;
    private static $inited;

    protected static function init()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('GroupTest Main Directory')));
        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        self::$group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('GroupTest Main Group'), 'description' => 'Main Group Description'));
        self::$directory->createGroup(self::$group);

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
        $group = \Stormpath\Resource\Group::get(self::$group->href);

        $this->assertInstanceOf('\Stormpath\Resource\Group', $group);
        $this->assertContains('Main_Group', $group->name);
        $this->assertContains('Main Group Description', $group->description);
        $this->assertInstanceOf('\Stormpath\Resource\Tenant', $group->tenant);
        $this->assertEquals(self::$client->tenant->name, $group->tenant->name);
        $this->assertInstanceOf('\Stormpath\Resource\AccountList', $group->accounts);
        $this->assertInstanceOf('\Stormpath\Resource\GroupMembershipList', $group->accountMemberships);
        $this->assertInstanceOf('\Stormpath\Resource\Directory', $group->directory);
        $this->assertEquals(self::$directory->name, $group->directory->name);
    }

    public function testSave()
    {
        $group = self::$group;

        $group->name = makeUniqueName('GroupTest testSave');
        $group->description = 'Main Group Description changed';
        $group->status = 'disabled';

        $group->save();

        $group = \Stormpath\Resource\Group::get(self::$group->href);

        $this->assertContains('testSave', $group->name);
        $this->assertContains('Main Group Description changed', $group->description);
        $this->assertEquals('DISABLED', $group->status);

    }

    public function testAddAccount()
    {
        $group = self::$group;

        $email = makeUniqueName('GroupTest addAccount') .'@testmail.stormpath.com';
        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'email' => $email,
                                                                  'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $group->addAccount($account);

        $accountFound = false;

        foreach($group->accounts as $acc)
        {
            if ($email == $acc->email)
            {
                $accountFound = true;
                break;
            }
        }

        $account->delete();

        $this->assertTrue($accountFound);

    }

    public function testAddingCustomData()
    {
        $cd = self::$group->customData;

        $cd->unitTest = "unit Test";
        $cd->save();

        $group = \Stormpath\Resource\Group::get(self::$group->href);
        $customData = $group->customData;
        $this->assertEquals('unit Test', $customData->unitTest);



    }

    public function testUpdatingCustomData()
    {
        $cd = self::$group->customData;

        $cd->unitTest = "some change";
        $cd->save();

        $group = \Stormpath\Resource\Group::get(self::$group->href);
        $customData = $group->customData;
        $this->assertEquals('some change', $customData->unitTest);

    }

    public function testRemovingCustomData()
    {
        $cd = self::$group->customData;

        $cd->remove('unitTest');

        $newClient = self::newClientInstance();
        $group = $newClient->dataStore->getResource(self::$group->href, Stormpath::GROUP);
        $customData = $group->customData;
        $this->assertNull($customData->unitTest);
    }

    public function testDeletingAllCustomData()
    {
        $cd = self::$group->customData;
        $cd->unitTest = "some change";
        $cd->rank = "Captain";
        $cd->birthDate = "2305-07-13";
        $cd->favoriteDrink = "favoriteDrink";
        $cd->save();

        $cd->delete();

        $newClient = self::newClientInstance();
        $group = $newClient->dataStore->getResource(self::$group->href, Stormpath::GROUP);
        $customData = $group->customData;
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
        $group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('GroupTest testDelete')));
        self::$directory->createGroup($group);

        $group = \Stormpath\Resource\Group::get($group->href);

        $this->assertInstanceOf('\Stormpath\Resource\Group', $group);
        $this->assertContains('testDelete', $group->name);

        $href = $group->href;

        $group->delete();

        \Stormpath\Resource\Group::get($href);
    }

    public function testShouldBeAbleToGetGroupViaHTMLFragment()
    {
        $group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('GroupTest htmlFragment')));
        self::$directory->createGroup($group);

        $href = $group->href;

        $hrefParts = array_reverse(explode('/',$href));

        $group = \Stormpath\Resource\Group::get($hrefParts[0]);

        $this->assertInstanceOf('\Stormpath\Resource\Group', $group);
        $this->assertEquals($href, $group->href);

        $group2 = \Stormpath\Client::get($hrefParts[1].'/'.$hrefParts[0], Stormpath::GROUP);

        $this->assertInstanceOf('\Stormpath\Resource\Group', $group2);
        $this->assertEquals($href, $group2->href);

        $group->delete();


    }

}