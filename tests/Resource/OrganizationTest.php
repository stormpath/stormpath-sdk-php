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
use Stormpath\Resource\Application;
use Stormpath\Resource\Organization;
use Stormpath\Resource\Tenant;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class OrganizationTest extends TestCase
{

    private static $organization = null;
    private static $directory;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Client::tearDown();
        Client::$cacheManager = 'Null';
        self::createOrganization();
        $directory = \Stormpath\Resource\Directory::instantiate([
            'name' => makeUniqueName('OrgTest'),
            'description' => 'Description of Main Directory',
            'status' => 'enabled'
        ]);
        self::$directory = self::createResource(\Stormpath\Resource\Directory::PATH, $directory);
    }

    public static function createOrganization()
    {
        self::$organization = Organization::instantiate([
            'name' => makeUniqueName('OrganizationForTests'),
            'nameKey' => 'nk'.md5(uniqid()),
            'description' => 'Organization used for the tests in OrganizationTest for PHP SDK'
        ]);

        self::createResource(Organization::PATH, self::$organization);



    }

    /**
     * @test
     */
    public function it_can_create_an_organization()
    {
        $organization = \Stormpath\Resource\Organization::create(array('name' => makeUniqueName('OrganizationTest testCreate'),'nameKey' => 'nk'.md5(uniqid())));

        $this->assertInstanceOf('Stormpath\Resource\Organization', $organization);
        $this->assertContains('testCreate', $organization->name);

        // testing the creation from an organization instance
        $organization2 = \Stormpath\Resource\Organization::instantiate(array('name' => makeUniqueName('OrganizationTest testCreate2'),'nameKey' => 'nk'.md5(uniqid())));
        \Stormpath\Resource\Organization::create($organization2);

        $this->assertInstanceOf('Stormpath\Resource\Organization', $organization2);
        $this->assertContains('testCreate2', $organization2->name);

        $organization->delete();
        $organization2->delete();


    }



    /**
     * @test
     */
    public function it_can_get_all_properties()
    {
        $this->assertNotNull(self::$organization->createdAt);
        $this->assertNotNull(self::$organization->description);
        $this->assertNotNull(self::$organization->description);
        $this->assertNotNull(self::$organization->href);
        $this->assertNotNull(self::$organization->modifiedAt);
        $this->assertNotNull(self::$organization->name);
        $this->assertNotNull(self::$organization->nameKey);
        $this->assertNotNull(self::$organization->status);
        $this->assertNotNull(self::$organization->tenant);
        $this->assertNull(self::$organization->defaultAccountStoreMapping);
        $this->assertNull(self::$organization->defaultGroupStoreMapping);
        $this->assertInstanceof('\Stormpath\Resource\Account', self::$organization->accounts);
        $this->assertInstanceOf('\Stormpath\Resource\GroupList', self::$organization->groups);
    }

    /**
     * @test
     */
    public function it_can_get_the_organization_off_the_data_store()
    {
        $org = Client::getInstance()->dataStore->getResource(self::$organization->href, Stormpath::ORGANIZATION);
        $this->assertEquals(self::$organization->href, $org->href);
    }

    /**
     * @test
     */
    public function it_can_get_the_organization_from_organization_class()
    {
        $org = Organization::get(self::$organization->href);
        $this->assertEquals(self::$organization->href,$org->href);
    }

    /**
     * @test
     */
    public function it_can_change_the_status_of_the_organization()
    {
        $this->assertEquals(Stormpath::ENABLED, self::$organization->status);
        self::$organization->status = Stormpath::DISABLED;
        self::$organization->save();
        $org = Organization::get(self::$organization->href);
        $this->assertEquals(Stormpath::DISABLED, $org->status);
        self::$organization->status = Stormpath::ENABLED;
        self::$organization->save();
        $org = Organization::get(self::$organization->href);
        $this->assertEquals(Stormpath::ENABLED, $org->status);
    }

    /**
     * @test
     */
    public function it_can_change_values_of_itself()
    {
        self::$organization->name = makeUniqueName('Changed Name');
        self::$organization->nameKey = 'something'.md5(uniqid());
        self::$organization->description = 'Some Description for PHP tests';
        self::$organization->save();

        $org = Organization::get(self::$organization->href);
        
        $this->assertContains('Changed_Name', $org->name);
        $this->assertContains('something', $org->nameKey);
        $this->assertEquals('Some Description for PHP tests', $org->description);

    }
    
    /**
     * @test
     */
    public function it_can_add_an_account_store_mapping()
    {


        $application = \Stormpath\Resource\Application::instantiate([
            'name' => makeUniqueName('OrgTest'),
            'description' => 'Description of Main App',
            'status' => 'enabled'
        ]);
        $application = self::createResource(\Stormpath\Resource\Application::PATH, $application);

        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::instantiate([
            'organization' => self::$organization,
            'accountStore' => self::$directory,
            'isDefaultAccountStore' => true,
            'isDefaultGroupStore' => true
        ]);

        $test1 = self::$organization->createOrganizationAccountStoreMapping($accountStoreMapping);
        $this->assertInstanceOf('\Stormpath\Resource\AccountStoreMapping', $test1);

        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::instantiate([
            'accountStore' => self::$organization,
            'application' => $application,
            'isDefaultAccountStore' => true
        ]);

        $test2 = $application->createAccountStoreMapping($accountStoreMapping);
        $this->assertInstanceOf('\Stormpath\Resource\AccountStoreMapping', $test2);

        $org = Organization::get(self::$organization->href);
        
        $this->assertNotNull($org->accountStoreMappings->href);
        $this->assertInstanceOf('Stormpath\Resource\AccountStore', $org->defaultAccountStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\AccountStore', $org->defaultGroupStoreMapping);


        $asm = $org->accountStoreMappings;
        foreach($asm as $mapping) {
            $this->assertEquals($org->href, $mapping->organization->href);
            $this->assertEquals(self::$directory->href, $mapping->accountStore->href);
        }

        $app = Application::get($application->href);

        $this->assertNotNull($app->accountStoreMappings->href);

        $asm = $app->accountStoreMappings;
        foreach($asm as $mapping) {
            $this->assertEquals($app->href, $mapping->application->href);
            $this->assertEquals(self::$organization->href, $mapping->accountStore->href);
        }

        $application->delete();
    }

    /**
     * @test
     * @expectedException \Stormpath\Resource\ResourceError
     * @expectedExceptionMessage The requested resource does not exist.
     */
    public function it_can_delete_an_organization()
    {
        $href =self::$organization->href;
        self::$organization->delete();

        Organization::get($href);
    }

    /** @test */
    public function an_account_can_be_added_to_organization()
    {
        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'surname' => 'Surname',
            'email' => makeUniqueName('DirectoryTest createAccount') . '@unknown123.kot',
            'password' => 'superP4ss'));

        $organization = Organization::instantiate([
            'name' => makeUniqueName('OrganizationForTests'),
            'nameKey' => 'nk'.md5(uniqid()),
            'description' => 'Organization used for the tests in OrganizationTest for PHP SDK'
        ]);

        $directory = \Stormpath\Resource\Directory::instantiate([
            'name' => makeUniqueName('OrgTest'),
            'description' => 'Description of Main Directory',
            'status' => 'enabled'
        ]);
        $organization = self::createResource(Organization::PATH, $organization);
        $directory = self::createResource(\Stormpath\Resource\Directory::PATH, $directory);

        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::instantiate([
            'organization' => $organization,
            'accountStore' => $directory,
            'isDefaultAccountStore' => true,
            'isDefaultGroupStore' => true
        ]);

        $organization->createOrganizationAccountStoreMapping($accountStoreMapping);


        $organization->createAccount($account, array('registrationWorkflowEnabled' => false));

        $account = \Stormpath\Resource\Account::get($account->href);

        $this->assertContains('OrgTest', $account->directory->name);
        $this->assertEquals('Account Name', $account->givenName);

        $directory->delete();
        $organization->delete();
    }


    public static function tearDownAfterClass()
    {
        if(self::$organization)
            self::$organization->delete();

        if(self::$directory);
            self::$directory->delete();

        parent::tearDownAfterClass();
    }
}
