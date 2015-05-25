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


class AccountStoreMappingTest extends \Stormpath\Tests\BaseTest {

    private static $application;
    private static $directory;
    private static $accountStoreMappingWithDir;
    private static $accountStoreMappingWithGroup;
    private static $inited;

    protected static function init()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => md5(time())));
        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        $group = \Stormpath\Resource\Group::instantiate(array('name' => md5(time())));
        self::$directory->createGroup($group);

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => md5(time())));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application);

        self::$accountStoreMappingWithDir = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => self::$directory));
        self::$application->createAccountStoreMapping(self::$accountStoreMappingWithDir);

        self::$accountStoreMappingWithGroup = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => $group));
        self::$application->createAccountStoreMapping(self::$accountStoreMappingWithGroup);

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
        if (self::$application)
        {
            self::$application->delete();
        }

        if (self::$directory)
        {
            self::$directory->delete();
        }
    }

    public function testGet()
    {
        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::get(self::$accountStoreMappingWithDir->href);

        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $accountStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\Directory', $accountStoreMapping->accountStore);

        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::get(self::$accountStoreMappingWithGroup->href);

        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $accountStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\Group', $accountStoreMapping->accountStore);

    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testGetNotFound()
    {
        \Stormpath\Resource\AccountStoreMapping::get('unknown');
    }

    public function testGetOptions() {

        $options = array('expand' => 'accountStore');
        $accountStoreMappingWithDir = \Stormpath\Resource\AccountStoreMapping::get(self::$accountStoreMappingWithDir->href, $options);

        // testing the expansion
        $this->assertTrue(count(array_intersect(array('name', 'description', 'status'), $accountStoreMappingWithDir->accountStore->propertyNames)) == 3);

    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testGetWithBadOptions() {

        // bad expansion format
        $options = array('expand' => 'application(offset:0)');
        \Stormpath\Resource\AccountStoreMapping::get(self::$accountStoreMappingWithDir->href, $options);
    }

    public function testSave()
    {
        $accountStoreMapping = self::$accountStoreMappingWithDir;
        $accountStoreMapping->listIndex = 0;
        $accountStoreMapping->defaultAccountStore = true;
        $accountStoreMapping->defaultGroupStore = true;

        $accountStoreMapping->save();

        $this->assertEquals(0, $accountStoreMapping->listIndex);
        $this->assertEquals(true, $accountStoreMapping->defaultAccountStore);
        $this->assertEquals(true, $accountStoreMapping->defaultGroupStore);
        $this->assertEquals(self::$application->name, $accountStoreMapping->application->name);
        $this->assertTrue($accountStoreMapping->isDefaultAccountStore());
        $this->assertTrue($accountStoreMapping->isDefaultGroupStore());
    }

    public function testCreate()
    {
        $application = \Stormpath\Resource\Application::instantiate(array('name' =>  "App" .md5(time())));
        self::createResource(\Stormpath\Resource\Application::PATH, $application);

        $accountStoreMappingWithDir = \Stormpath\Resource\AccountStoreMapping::instantiate();
        $accountStoreMappingWithDir->accountStore = self::$directory;
        $accountStoreMappingWithDir->application = $application;
        \Stormpath\Resource\AccountStoreMapping::create($accountStoreMappingWithDir);

        $href = $accountStoreMappingWithDir->href;
        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::get($href);

        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $accountStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\Directory', $accountStoreMapping->accountStore);
        $this->assertContains('App', $accountStoreMapping->application->name);

        $accountStoreMapping->delete();
        $application->delete();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateWithBadApplicationInstance()
    {
        \Stormpath\Resource\AccountStoreMapping::create(array('accountStore' => self::$directory));
    }

    public function testDelete()
    {
        $application = \Stormpath\Resource\Application::instantiate(array('name' =>  "App" .md5(time())));
        self::createResource(\Stormpath\Resource\Application::PATH, $application);

        $accountStoreMappingWithDir = \Stormpath\Resource\AccountStoreMapping::create(array('accountStore' => self::$directory, 'application' => $application));

        $href = $accountStoreMappingWithDir->href;
        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::get($href);

        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $accountStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\Directory', $accountStoreMapping->accountStore);

        $accountStoreMapping->delete();

        try
        {
            \Stormpath\Resource\AccountStoreMapping::get($href);
            $application->delete();
            $this->fail('Should have thrown a ResourceError.');
        } catch (\Stormpath\Resource\ResourceError $re)
        {
            $this->assertTrue(true);
        }

        $application->delete();
    }

}