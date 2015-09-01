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


use Stormpath\Resource\Directory;
use Stormpath\Resource\FacebookProvider;
use Stormpath\Resource\GoogleProvider;
use Stormpath\Resource\Tenant;
use Stormpath\Stormpath;

class TenantTest extends \Stormpath\Tests\BaseTest {

    public function testGet()
    {
        $tenant = \Stormpath\Resource\Tenant::get();

        $this->assertEquals(self::$client->tenant->name, $tenant->name);
        $this->assertEquals(self::$client->tenant->key, $tenant->key);
        $this->assertInstanceOf('\Stormpath\Resource\DirectoryList', $tenant->directories);
        $this->assertInstanceOf('\Stormpath\Resource\ApplicationList', $tenant->applications);
    }

    public function testCreateApplication()
    {
        $tenant = self::$client->tenant;

        $application = \Stormpath\Resource\Application::instantiate(array('name' => makeUniqueName('TenantTest CreateApp')));

        $tenant->createApplication($application);

        $this->assertInstanceOf('\Stormpath\Resource\Application', $application);
        $this->assertEquals($tenant->name, $application->tenant->name);
        $this->assertContains('CreateApp', $application->name);

        foreach($tenant->applications as $app)
        {
            $this->assertInstanceOf('\Stormpath\Resource\Application', $app);
        }

        $application->delete();
    }

    public function testAddingCustomData()
    {
        $cd = self::$client->tenant->customData;

        $cd->unitTest = "unit Test";
        $cd->save();

        $tenant = \Stormpath\Resource\Tenant::get();
        $customData = $tenant->customData;
        $this->assertEquals('unit Test', $customData->unitTest);



    }

    public function testUpdatingCustomData()
    {
        $cd = self::$client->tenant->customData;

        $cd->unitTest = "some change";
        $cd->save();

        $tenant = \Stormpath\Resource\Tenant::get();
        $customData = $tenant->customData;
        $this->assertEquals('some change', $customData->unitTest);

    }

    public function testRemovingCustomData()
    {
        $cd = self::$client->tenant->customData;

        $cd->remove('unitTest');

        $tenant = \Stormpath\Resource\Tenant::get();
        $customData = $tenant->customData;
        $this->assertNull($customData->unitTest);
    }

    public function testDeletingAllCustomData()
    {
        $cd = self::$client->tenant->customData;
        $cd->unitTest = "some change";
        $cd->rank = "Captain";
        $cd->birthDate = "2305-07-13";
        $cd->favoriteDrink = "favoriteDrink";
        $cd->save();

        $cd->delete();

        $tenant = \Stormpath\Resource\Tenant::get();
        $customData = $tenant->customData;
        $this->assertNull($customData->unitTest);
        $this->assertNull($customData->rank);
        $this->assertNull($customData->birthDate);
        $this->assertNull($customData->favoriteDrink);
    }

    public function testCreateDirectory()
    {
        $tenant = self::$client->tenant;

        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('TenantTest createDirectory')));

        $tenant->createDirectory($directory);

        $this->assertInstanceOf('\Stormpath\Resource\Directory', $directory);
        $this->assertEquals($tenant->name, $directory->tenant->name);
        $this->assertContains('createDirectory', $directory->name);

        foreach($tenant->directories as $dir)
        {
            $this->assertInstanceOf('\Stormpath\Resource\Directory', $dir);
        }

        $directory->delete();
    }

    public function testCreateDirectoryWithGoogleProvider()
    {
        $dataStore = $this->getMock('\Stormpath\DataStore\InternalDataStore');

        $properties = new \stdClass();
        $properties->href = "https://api.stormpath.com/v1/tenants/jaef0wq38ruojoiadE";
        $properties->applications = new \stdClass();
        $properties->applications->href = "https://api.stormpath.com/v1/tenants/jaef0wq38ruojoiadE/applications";
        $properties->directories = new \stdClass();
        $properties->directories->href = "https://api.stormpath.com/v1/tenants/jaef0wq38ruojoiadE/directories";

        $tenant = new Tenant($dataStore, $properties);

        $provider = self::$client->dataStore->instantiate(\Stormpath\Stormpath::GOOGLE_PROVIDER);
        $provider->setClientId("288130632849-9h8uep5g95cebi3nou1am4j73gsmq24l.apps.googleusercontent.com");
        $provider->setClientSecret("OrXUzgCGMfjmXpKqiRS4-hDN");
        $provider->setRedirectUri("https://www.example.com/oauth2callback");

        $directory = self::$client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
        $directory->setName("my-google-directory-2");
        $directory->setDescription("A Google directory");
        $directory->setProvider($provider);

        $this->assertEquals($directory->getProvider()->getProviderId(), GoogleProvider::GOOGLE_PROVIDER_ID);

        $expectedDirectory = $this->getMock('\Stormpath\Resource\Directory');
        $dataStore->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('/'.Directory::PATH),
                $this->equalTo($directory),
                $this->equalTo(Stormpath::DIRECTORY),
                $this->equalTo(array())
            )
            ->will($this->returnValue($expectedDirectory));

        $returnedDirectory = $tenant->createDirectory($directory);
        $this->assertEquals($expectedDirectory, $returnedDirectory);
    }

    public function testCreateDirectoryWithFacebookProvider()
    {
        $dataStore = $this->getMock('\Stormpath\DataStore\InternalDataStore');

        $properties = new \stdClass();
        $properties->href = "https://api.stormpath.com/v1/tenants/jaef0wq38ruojoiadE";
        $properties->applications = new \stdClass();
        $properties->applications->href = "https://api.stormpath.com/v1/tenants/jaef0wq38ruojoiadE/applications";
        $properties->directories = new \stdClass();
        $properties->directories->href = "https://api.stormpath.com/v1/tenants/jaef0wq38ruojoiadE/directories";

        $tenant = new Tenant($dataStore, $properties);

        $provider = self::$client->dataStore->instantiate(\Stormpath\Stormpath::FACEBOOK_PROVIDER);
        $provider->setClientId("1011854538839621");
        $provider->setClientSecret("82c16954b0d88216127d66ac44bbc3a8");
        $provider->setRedirectUri("https://www.example.com/oauth2callback");

        $directory = self::$client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
        $directory->setName("my-fb-directory");
        $directory->setDescription("A Facebook directory");
        $directory->setProvider($provider);

        $this->assertEquals(FacebookProvider::FACEBOOK_PROVIDER_ID, $directory->getProvider()->getProviderId());

        $expectedDirectory = $this->getMock('\Stormpath\Resource\Directory');
        $dataStore->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('/'.Directory::PATH),
                $this->equalTo($directory),
                $this->equalTo(Stormpath::DIRECTORY),
                $this->equalTo(array())
            )
            ->will($this->returnValue($expectedDirectory));

        $returnedDirectory = $tenant->createDirectory($directory);
        $this->assertEquals($expectedDirectory, $returnedDirectory);
    }

}