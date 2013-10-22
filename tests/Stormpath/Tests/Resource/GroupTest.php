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


class GroupTest extends \Stormpath\Tests\BaseTest {

    private static $directory;
    private static $group;
    private static $inited;

    protected static function init()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => 'Main Directory' .md5(time())));
        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        self::$group = \Stormpath\Resource\Group::instantiate(array('name' => 'Main Group' . md5(time()), 'description' => 'Main Group Description'));
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
    }

    public function testGet()
    {
        $group = \Stormpath\Resource\Group::get(self::$group->href);

        $this->assertInstanceOf('\Stormpath\Resource\Group', $group);
        $this->assertContains('Main Group', $group->name);
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

        $group->name = 'Main Group Changed' . md5(time());
        $group->description = 'Main Group Description changed';
        $group->status = 'disabled';

        $group->save();

        $group = \Stormpath\Resource\Group::get(self::$group->href);

        $this->assertContains('Main Group Changed', $group->name);
        $this->assertContains('Main Group Description changed', $group->description);
        $this->assertEquals('DISABLED', $group->status);

    }

    public function testAddAccount()
    {
        $group = self::$group;

        $email = md5(time()) .'@unknown123.kot';
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

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testDelete()
    {
        $group = \Stormpath\Resource\Group::instantiate(array('name' => 'Deletable Group' . md5(time())));
        self::$directory->createGroup($group);

        $group = \Stormpath\Resource\Group::get($group->href);

        $this->assertInstanceOf('\Stormpath\Resource\Group', $group);
        $this->assertContains('Deletable Group', $group->name);

        $href = $group->href;

        $group->delete();

        \Stormpath\Resource\Group::get($href);
    }

}