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


class GroupMembershipTest extends \Stormpath\Tests\TestCase {

    private static $directory;
    private static $group;
    private static $account;
    private static $groupMembership;
    private static $inited;

    protected static function init()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('GroupMembershipTest Main Directory')));
        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        self::$group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('GroupMembershipTest Main Group')));

        self::$directory->createGroup(self::$group);

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                        'surname' => 'Surname',
                                                                        'email' => makeUniqueName('GroupMembershipTest') .'@testmail.stormpath.com',
                                                                        'password' => 'superP4ss'));

        self::$directory->createAccount(self::$account);

        self::$group->addAccount(self::$account);


        foreach(self::$group->accountMemberships as $accMembership)
        {
            self::$groupMembership = $accMembership;
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
        if (self::$groupMembership)
        {
            self::$groupMembership->delete();
        }

        if (self::$directory)
        {
            self::$directory->delete();
        }

        parent::tearDownAfterClass();
    }

    public function testGet()
    {
        $groupMembership = \Stormpath\Resource\GroupMembership::get(self::$groupMembership->href);

        $this->assertInstanceOf('\Stormpath\Resource\GroupMembership', $groupMembership);
        $this->assertContains('Main_Group', $groupMembership->group->name);
        $this->assertContains('@testmail.stormpath.com', $groupMembership->account->email);
    }

    public function testCreate()
    {
        $group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('GroupMembershipTest testCreate')));

        self::$directory->createGroup($group);

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'email' => makeUniqueName('GroupMembershipTest testCreate') .'@testmail.stormpath.com',
                                                                  'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $groupMembership = \Stormpath\Resource\GroupMembership::instantiate();
        $groupMembership->account = $account;
        $groupMembership->group = $group;
        $groupMembership = \Stormpath\Resource\GroupMembership::create($groupMembership);

        $groupMembership = \Stormpath\Resource\GroupMembership::get($groupMembership->href);

        $this->assertInstanceOf('\Stormpath\Resource\GroupMembership', $groupMembership);
        $this->assertContains('testCreate', $groupMembership->group->name);
        $this->assertContains('@testmail.stormpath.com', $groupMembership->account->email);

    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testDelete()
    {
        $group = \Stormpath\Resource\Group::instantiate(array('name' => makeUniqueName('GroupMembershipTest testDelete')));

        self::$directory->createGroup($group);

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'email' => makeUniqueName('GroupMembershipTest testDelete') .'@testmail.stormpath.com',
                                                                  'password' => 'superP4ss'));

        self::$directory->createAccount($account);

        $groupMembership = \Stormpath\Resource\GroupMembership::create(array('account' => $account, 'group' => $group));

        $groupMembership = \Stormpath\Resource\GroupMembership::get($groupMembership->href);

        $this->assertInstanceOf('\Stormpath\Resource\GroupMembership', $groupMembership);
        $this->assertContains('testDelete', $groupMembership->group->name);
        $this->assertContains('@testmail.stormpath.com', $groupMembership->account->email);

        $href = $groupMembership->href;

        $groupMembership->delete();

        \Stormpath\Resource\GroupMembership::get($href);
    }

}