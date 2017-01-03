<?php
/**
 * Copyright 2017 Stormpath, Inc.
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
 *
 */

namespace Stormpath\Tests\Http\Authc;

use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class RequestSignerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        \Stormpath\Client::tearDown();
    }

    public function tearDown()
    {
        \Stormpath\Client::tearDown();
        parent::tearDown();
    }
    /**
     * @test
     */
    public function it_can_sign_a_request_with_basic_authorization_header()
    {
        \Stormpath\Client::$authenticationScheme = Stormpath::BASIC_AUTHENTICATION_SCHEME;
        $client = \Stormpath\Client::getInstance();

        $directory = $this->createDirectory();

        $this->assertInstanceOf('\\Stormpath\\Resource\\Directory', $directory);

        $directory->delete();
    }
    /**
     * @test
     */
    public function it_can_sign_a_request_with_sauthc1_authorization_header()
    {
        \Stormpath\Client::$authenticationScheme = Stormpath::SAUTHC1_AUTHENTICATION_SCHEME;
        $client = \Stormpath\Client::getInstance();

        $directory = $this->createDirectory();

        $this->assertInstanceOf('\\Stormpath\\Resource\\Directory', $directory);

        $directory->delete();
    }

    private function createDirectory()
    {
        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('Directory For Request Signer Test'), 'description' => 'Main Directory description'));
        return self::createResource(\Stormpath\Resource\Directory::PATH, $directory);
    }
}
