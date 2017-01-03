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

use Stormpath\Authc\Api\OAuthClientCredentialsRequestAuthenticator;
use Stormpath\Authc\Api\Request;
use Stormpath\Exceptions\RequestAuthenticatorException;
use Stormpath\Tests\TestCase;

class OAuthClientCredentialsAuthenticationTest extends TestCase
{

    public static $account;

    private static $application;

    private static $apiKey;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = \Stormpath\Resource\Application::instantiate(
            array(
                'name' => makeUniqueName('Application OAuthClientCredentials'),
                'description' => 'Application for OAuthClientCredentialsAuthenticationTest',
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
                'username' => makeUniqueName('OAuthClientCredentialsAuthenticationTest'),
                'email' => makeUniqueName('OAuthClientCredentialsAuthenticationTest') .'@testmail.stormpath.com',
                'password' => 'superP4ss'

            )
        );
        self::$application->createAccount(self::$account);

        self::$apiKey = self::$account->createApiKey();


    }


    /**
     * @test
     * @expectedException Stormpath\Exceptions\RequestAuthenticatorException
     * @expectedExceptionMessage The API Key is not valid for this request.
     */
    public function it_throws_exception_if_api_key_is_null_or_secret_is_incorrect()
    {

        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':123');
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = 'http://test.com/?grant_type=client_credentials';
        $_SERVER['QUERY_STRING'] = 'grant_type=client_credentials';

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthClientCredentialsRequestAuthenticator(self::$application);
        $auth->authenticate(Request::createFromGlobals());

    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\RequestAuthenticatorException
     * @expectedExceptionMessage The API Key is not allowed to make this request.
     */
    public function it_throws_exception_if_the_api_key_is_disabled()
    {

        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = 'http://test.com/?grant_type=client_credentials';
        $_SERVER['QUERY_STRING'] = 'grant_type=client_credentials';

        self::$apiKey->setStatus('DISABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthClientCredentialsRequestAuthenticator(self::$application);
        $auth->authenticate(Request::createFromGlobals());


    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\RequestAuthenticatorException
     * @expectedExceptionMessage The Account you are authenticating with is not active.
     */
    public function it_throws_exception_if_the_account_is_disabled()
    {
        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = 'http://test.com/?grant_type=client_credentials';
        $_SERVER['QUERY_STRING'] = 'grant_type=client_credentials';

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('DISABLED');
        self::$account->save();

        $auth = new OAuthClientCredentialsRequestAuthenticator(self::$application);
        $auth->authenticate(Request::createFromGlobals());
    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\RequestAuthenticatorException
     * @expectedExceptionMessage The grant_type query parameter must be used
     */
    public function it_throws_exception_if_grant_type_is_not_set()
    {
        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = 'http://test.com/?some_other_thing=something_else';
        $_SERVER['QUERY_STRING'] = 'some_other_thing=something_else';

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthClientCredentialsRequestAuthenticator(self::$application);
        $auth->authenticate(Request::createFromGlobals());
    }

    /**
     * @test
     */
    public function it_can_authenticate_and_return_oauth_client_credentials_result()
    {
        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = 'http://test.com/?grant_type=client_credentials';
        $_SERVER['QUERY_STRING'] = 'grant_type=client_credentials';

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthClientCredentialsRequestAuthenticator(self::$application);
        $result = $auth->authenticate(Request::createFromGlobals());
        $token = json_decode($result->getAccessToken());

        $this->assertInstanceOf('Stormpath\Authc\Api\OAuthClientCredentialsAuthenticationResult', $result);

        $this->assertInstanceOf('Stormpath\Resource\Application', $result->getApplication());
        $this->assertInstanceOf('Stormpath\Resource\ApiKey', $result->getApiKey());
        $this->assertObjectHasAttribute('access_token', $token);
        $this->assertObjectHasAttribute('token_type', $token);
        $this->assertObjectHasAttribute('expires_in', $token);
    }

    protected function tearDown()
    {
        Request::tearDown();
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