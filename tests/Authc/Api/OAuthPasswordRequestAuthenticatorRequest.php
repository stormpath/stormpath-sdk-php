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

namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\OAuthPasswordRequestAuthenticator;
use Stormpath\Tests\TestCase;

class OAuthPasswordAuthenticationTest extends TestCase
{
    public static $account;

    private static $application;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = \Stormpath\Resource\Application::instantiate(
            array(
                'name' => 'Main App for the tests' . md5(time()),
                'description' => 'Description of Main App',
                'status' => 'enabled'
            )
        );
        parent::createResource(
            \Stormpath\Resource\Application::PATH,
            self::$application,
            array('createDirectory' => true)
        );

        self::$account = \Stormpath\Resource\Account::instantiate(
            array(
                'givenName' => 'PHP',
                'middleName' => 'BasicRequestAuthenticator',
                'surname' => 'Test',
                'username' => md5(time() . microtime() . uniqid()) . 'username',
                'email' => md5(time() . microtime() . uniqid()) . '@testmail.stormpath.com',
                'password' => 'superP4ss'

            )
        );
        self::$application->createAccount(self::$account);
    }

	/** @test */
	public function
	{

	}


    public static function tearDownAfterClass()
    {
        if (self::$application)
        {
            $accountStoreMappings = self::$application->accountStoreMappings;

            if ($accountStoreMappings)
            {
                foreach($accountStoreMappings as $asm)
                {
                    $accountStore = $asm->accountStore;
                    $asm->delete();
                    $accountStore->delete();
                }
            }

            self::$application->delete();
        }

        parent::tearDownAfterClass();
    }
}