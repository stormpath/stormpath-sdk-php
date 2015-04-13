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

        $application = \Stormpath\Resource\Application::instantiate(array('name' => 'App for this test' .md5(time())));

        $tenant->createApplication($application);

        $this->assertInstanceOf('\Stormpath\Resource\Application', $application);
        $this->assertEquals($tenant->name, $application->tenant->name);
        $this->assertContains('App for this test', $application->name);

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

        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => 'Dir for this test' .md5(time())));

        $tenant->createDirectory($directory);

        $this->assertInstanceOf('\Stormpath\Resource\Directory', $directory);
        $this->assertEquals($tenant->name, $directory->tenant->name);
        $this->assertContains('Dir for this test', $directory->name);

        foreach($tenant->directories as $dir)
        {
            $this->assertInstanceOf('\Stormpath\Resource\Directory', $dir);
        }

        $directory->delete();
    }

}