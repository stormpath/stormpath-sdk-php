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

class AccountTest extends \Stormpath\Tests\BaseTest {

    private static $directory;
    private static $groups;
    private static $account;
    const GROUPS_COUNT = 45;

    public static function setUpBeforeClass() {

        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => md5(time())));

        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                        'middleName' => 'Middle Name',
                                                                        'surname' => 'Surname',
                                                                        'username' => md5(time()) . 'username',
                                                                        'email' => md5(time()) .'@unknown123.kot',
                                                                        'password' => 'superP4ss'));

        self::$directory->createAccount(self::$account);

        self:$groups = array();

        $groupsCount = 0;
        while($groupsCount < self::GROUPS_COUNT)
        {
            $group = \Stormpath\Resource\Group::instantiate(array('name' => "$groupsCount Group Name"));
            self::$directory->createGroup($group);
            self::$account->addGroup($group);
            $groups[$groupsCount] = $group;
            $groupsCount++;
        }
    }

    public static function tearDownAfterClass()
    {
        if (self::$account)
        {
            self::$account->delete();
        }

        if (self::$directory)
        {
            self::$directory->delete();
        }
    }

    public function testGet() {

        $account = \Stormpath\Resource\Account::get(self::$account->href);

        $this->assertInstanceOf('Stormpath\Resource\Account', $account);

    }

    public function testGetOptions() {

        $options = array('expand' => 'groups(offset:5,limit:30)');
        $account = \Stormpath\Resource\Account::get(self::$account->href, $options);

        // testing that the groups collection was successfully expanded
        $this->assertEquals(30, count($account->groups->currentPage->items));

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
        $this->assertEquals(self::$client->tenant->name, $account->tenant->name);
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
        foreach($account->groupMemberShips as $groupMembership)
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

    public function testGroupsOptions()
    {
        $account = self::$account;

        $options = array('offset' => 1, 'limit' => 2, 'orderBy' => 'name desc', 'q' => '9', 'expand' => 'directory');
        $groups = $account->getGroups($options);

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
                $this->assertEquals('39 Group Name', $group->name);
            }

            if ($groupsCount == 2)
            {
                $this->assertEquals('29 Group Name', $group->name);
            }

            if ($groupsCount == 3)
            {
                $this->assertEquals('19 Group Name', $group->name);
            }

            // we should receive 4 groups that match the filter q=9,
            // but we set the offset at 1 so the total items we'll get should be 3.
            $this->assertTrue($groupsCount <= 3);
        }
    }

}