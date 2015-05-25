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