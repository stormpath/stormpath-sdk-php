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


use JWT;
use Stormpath\Client;
use Stormpath\Resource\Account;
use Stormpath\Resource\Application;
use Stormpath\Resource\Directory;
use Stormpath\Resource\Resource;
use Stormpath\Resource\ResourceError;
use Stormpath\Resource\VerificationEmailRequest;
use Stormpath\Resource\VerificationEmail;
use Stormpath\Stormpath;
use Stormpath\Util\UUID;

/** @group multipleApps */
class ApplicationTest extends \Stormpath\Tests\TestCase {

    private static $application;
    private static $inited;

    private static $directory;
    private static $account;

    protected static function init()
    {
        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => makeUniqueName('ApplicationTest'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));
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
        if (self::$application && self::$application->href)
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

    public function testGet()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('ApplicationTest', $application->name);
    }

    public function testCreateIdSiteURLReturnsURLWithJWT()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $redirectUrl = $application->createIdSiteUrl(array(
            'callbackUri' => 'https://stormpath.com',
            'state' => UUID::v4()
        ));

        $this->assertContains('.stormpath.com/sso?jwtRequest=', $redirectUrl);
    }


    public function testCreateIdSiteUrlReturnsCorrectPathForLogoutRequest()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $redirectUrl = $application->createIdSiteUrl(array(
            'callbackUri' => 'https://stormpath.com',
            'logout'=>true,
            'state' => UUID::v4()
        ));

        $this->assertContains('.stormpath.com/sso/logout?jwtRequest=', $redirectUrl);
    }

    public function testCreateIdSiteURLWithNameKeySettings()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $redirectUrl = $application->createIdSiteUrl(array(
            'callbackUri' => 'https://stormpath.com',
            'state' => UUID::v4(),
            'organizationNameKey' => 'testOrg',
            'useSubDomain' => true,
            'showOrganizationField' => true
        ));

        $this->assertContains('.stormpath.com/sso?jwtRequest=', $redirectUrl);
        $apiSecret = Client::getInstance()->getDataStore()->getApiKey()->getSecret();
        $parts = explode('=',$redirectUrl);
        JWT::$leeway = 10000;
        $decoded = JWT::decode($parts[1], $apiSecret, ['HS256']);

        $this->assertEquals('testOrg', $decoded->onk);
        $this->assertTrue($decoded->usd);
        $this->assertTrue($decoded->sof);
    }

    /**
     * @expectedException \Stormpath\Exceptions\IdSite\InvalidCallbackUriException
     */
    public function testCreateIdSiteUrlThrowsExceptionIfNoCallbackURIPrivided()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $redirectUrl = $application->createIdSiteUrl(array(
            'logout'=>true,
            'state' => UUID::v4()
        ));
    }

    /**
     * @test
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function itThrowsExceptionIfErrorIsProvidedInHandleUrl()
    {
        $apiSecret = Client::getInstance()->getDataStore()->getApiKey()->getSecret();

        // Create JWT Response with error
        $jwt = JWT::encode(
            array(
                'jti'=>'123123123',
                'iat'=>time(),
                'iss'=>'https://api.stormpath.com/v1/applications/someAppUidHere',
                'exp'=>time()+3600,
                'err'=>json_encode(
                    array(
                        'code'=>'11001',
                        'developerMessage'=>'testing',
                        'message'=>'testing message',
                        'moreInfo'=>'mailto:support@stormpath.com',
                        'status'=>401)
                )
            ),
            $apiSecret
        );
        // Handle ID Site Response
        self::$application->handleIdSiteCallback('http://example.com?jwtResponse='.$jwt);
    }

    /**
     * @test
     */
    public function itDoesNotThrowIDSiteExceptionIfErrIsNotPresent()
    {
        $apiSecret = Client::getInstance()->getDataStore()->getApiKey()->getSecret();

        // Create JWT Response with error
        $jwt = JWT::encode(
            array(
                "iss" => "https://formal-ring.id.stormpath.io",
                'sub'=>self::$account,
                "aud" => "1PN3FXI0U79E2MHCF6XUYGU4Z",
                "exp" => time()+100,
                "iat" => 1450221187,
                "jti" => "37Vljw5YV0dTNNP3V4h0SY",
                "irt" => "370640ef-ea7c-4532-94ed-b55dc7fa006a",
                "state" => "",
                "isNewSub" => false,
                "status" => "LOGOUT"
            ),
            $apiSecret
        );

        self::$application->handleIdSiteCallback('http://example.com?jwtResponse='.$jwt);
    }

    /** @test */
    public function itWillReturnNullInAccountIfLoggedOut()
    {
        $apiSecret = Client::getInstance()->getDataStore()->getApiKey()->getSecret();
        $jwt = JWT::encode(
            [
                "iss" => "https://formal-ring.id.stormpath.io",
                "sub" => null,
                "aud" => UUID::v4(),
                "exp" => time()+100,
                "iat" => 1450221187,
                "jti" => UUID::v4(),
                "irt" => UUID::v4(),
                "state" => "",
                "isNewSub" => false,
                "status" => "LOGOUT"
            ],
            $apiSecret
        );

        $result = self::$application->handleIdSiteCallback('http://example.com?jwtResponse='.$jwt);
        $this->assertNull($result->account);
    }

    protected function createAccount()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('ApplicationTest createAccount')));

        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('ApplicationTest createAccount') . 'username',
            'email' => makeUniqueName('ApplicationTest createAccount') .'@unknown123.kot',
            'password' => 'superP4ss'));

        self::$directory->createAccount(self::$account);
    }

    protected function deleteAccount()
    {
        if (self::$directory)
        {
            self::$directory->delete();
        }
    }

    protected function generateResponseUrl()
    {
        $jwt = array();
        $jwt['iss'] = 'https://stormpath.com';
        $jwt['sub'] = self::$account->href;
        $jwt['aud'] = UUID::v4();
        $jwt['exp'] = time() + 60;
        $jwt['iat'] = time();
        $jwt['jti'] = UUID::v4();
        $jwt['irt'] = UUID::v4();
        $jwt['state'] = "";
        $jwt['isNewSub'] = false;
        $jwt['status'] = "AUTHENTICATED";

        $apiSecret = Client::getInstance()->getDataStore()->getApiKey()->getSecret();

        $token = JWT::encode($jwt, $apiSecret);


        return 'https://stormpath.com?jwtResponse='.$token;


    }

    public function testHandleIdSiteCallbackReturnsExpectedItems()
    {
        $this->createAccount();

        $responseUrl = $this->generateResponseUrl();

        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $response = $application->handleIdSiteCallback($responseUrl);

        $this->assertEquals('AUTHENTICATED', $response->status);
        $this->assertFalse($response->isNew);
        $this->assertEquals("", $response->state);
        $this->assertEquals(self::$account->href, $response->account->href);


        $this->deleteAccount();


    }


    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testGetNotFound()
    {
        \Stormpath\Resource\Application::get('unknown');
    }

    public function testCreate()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => makeUniqueName('ApplicationTest testCreate')));

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('testCreate', $application->name);

        // testing the creation from an application instance
        $application2 = \Stormpath\Resource\Application::instantiate(array('name' => makeUniqueName('ApplicationTest testCreate2')));
        \Stormpath\Resource\Application::create($application2);

        $this->assertInstanceOf('Stormpath\Resource\Application', $application2);
        $this->assertContains('testCreate2', $application2->name);

        $application->delete();
        $application2->delete();
    }

    public function testGetters()
    {
        $application = self::$application;

        $this->assertContains('ApplicationTest', $application->name);
        $this->assertContains('Description of Main App', $application->description);
        $this->assertEquals(self::$client->tenant->name, $application->tenant->name);
        $this->assertInstanceOf('Stormpath\Resource\AccountList', $application->accounts);
        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $application->defaultAccountStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $application->defaultGroupStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\GroupList', $application->groups);
        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMappingList', $application->accountStoreMappings);
        $this->assertInstanceOf('Stormpath\Resource\BasicLoginAttempt', $application->loginAttempts);
        $this->assertInstanceOf('Stormpath\Resource\OauthPolicy', $application->oauthPolicy);

        foreach($application->accountStoreMappings as $acm)
        {
            $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $acm);
            $this->assertContains('ApplicationTest', $acm->accountStore->name);
        }
    }

    public function testCreateAccount()
    {
        $application = self::$application;

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => makeUniqueName('ApplicationTest testCreateAccount') . 'username',
                                                                  'email' => makeUniqueName('ApplicationTest testCreateAccount') .'@unknown123.kot',
                                                                  'password' => 'superP4ss'));

        $application->createAccount($account);

        $account = \Stormpath\Resource\Account::get($account->href);

        $this->assertEquals('Account Name', $account->givenName);

        $account->delete();
    }

    public function testCreateAccountWithCustomData()
    {
        $application = self::$application;

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('ApplicationTest testCreateAccountWithCustomData') . 'username',
            'email' => makeUniqueName('ApplicationTest testCreateAccountWithCustomData') .'@unknown123.kot',
            'password' => 'superP4ss'));

        $customData = $account->customData;
        $customData->phone = "12345";

        $account = $application->createAccount($account);

        $newClient = self::newClientInstance();
        $account = $newClient->dataStore->getResource($account->href, Stormpath::ACCOUNT);
        $this->assertEquals("12345", $account->customData->phone);

        $account->delete();
    }

    public function testCreateGroup()
    {
        $application = self::$application;

        $group = new \stdClass();
        $group->name = makeUniqueName('ApplicationTest testCreateGroup');

        $group = \Stormpath\Resource\Group::instantiate($group);
        $application->createGroup($group);

        $group = \Stormpath\Resource\Group::get($group->href);

        $this->assertContains('testCreateGroup', $group->name);

        $group->delete();
    }

    public function testCreateAccountStoreMapping()
    {
        $application = self::$application;

        $directory = \Stormpath\Resource\Directory::create(array('name' => makeUniqueName('ApplicationTest testCreateAccountStoreMapping')));

        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => $directory));

        $application->createAccountStoreMapping($accountStoreMapping);

        $this->assertContains('testCreateAccountStoreMapping', $accountStoreMapping->accountStore->name);

        $accountStoreMapping->delete();
        $directory->delete();
    }

    public function testSendPasswordResetEmail()
    {
        $application = self::$application;
        $email = makeUniqueName('ApplicationTest SendPasswordReset') .'@unknown123.kot';

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => 'super_unique_username',
                                                                  'email' => $email,
                                                                  'password' => 'superP4ss'));

        $application->createAccount($account);

        $account = $application->sendPasswordResetEmail($email);

        $this->assertEquals($email, $account->email);

        $account->delete();
    }
    
    public function testSendPasswordResetEmailWithAccountStore()
    {
        $application = self::$application;

        $groupA = new \stdClass();
        $groupA->name = makeUniqueName('ApplicationTest testSendPasswordResetEmailWithAccountStore A');
        $groupA = \Stormpath\Resource\Group::instantiate($groupA);
        $application->createGroup($groupA);

        $groupB = new \stdClass();
        $groupB->name = makeUniqueName('ApplicationTest testSendPasswordResetEmailWithAccountStore B');
        $groupB = \Stormpath\Resource\Group::instantiate($groupB);
        $application->createGroup($groupB);

        $accountStoreMappingA = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => $groupA));
        $application->createAccountStoreMapping($accountStoreMappingA);

        $accountStoreMappingB = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => $groupB));
        $application->createAccountStoreMapping($accountStoreMappingB);

        $email = makeUniqueName('ApplicationTest SendPassword') .'@unknown123.kot';
        $account = \Stormpath\Resource\Account::instantiate(array(
            'givenName' => 'Account Name',
            'surname' => 'Surname',
            'username' => 'super_unique_username',
            'email' => $email,
            'password' => 'superP4ss'));

        $application->createAccount($account);
        $groupA->addAccount($account);

        $account = $application->sendPasswordResetEmail($email,
            array("accountStore" => $accountStoreMappingA->getAccountStore()));
        $this->assertEquals($email, $account->email);


        $resetToken = $application->sendPasswordResetEmail($email,[],true);
        $this->assertInstanceOf('\Stormpath\Resource\PasswordResetToken', $resetToken);

        try {
            // lookup email address in an AccountStore that doesn't contain the corresponding account
            $account = $application->sendPasswordResetEmail($email,
                array("accountStore" => $accountStoreMappingB->getAccountStore()));

            $account->delete();
            $accountStoreMappingB->delete();
            $accountStoreMappingA->delete();
            $groupB->delete();
            $groupA->delete();

            $this->fail('sendPasswordResetEmail should have failed.');
        }
        catch (\Stormpath\Resource\ResourceError $re)
        {
            $this->assertEquals(400, $re->getStatus());
            $this->assertEquals(2016, $re->getErrorCode());
            $this->assertContains('does not match a known resource', $re->getMessage());
            $this->assertContains('does not match a known resource', $re->getDeveloperMessage());
            $this->assertContains('2016', $re->getMoreInfo());
        }

        $account->delete();
        $accountStoreMappingB->delete();
        $accountStoreMappingA->delete();
        $groupB->delete();
        $groupA->delete();
    }

    public function testCanResetPasswordFromSPToken()
    {
        $email = makeUniqueName('ApplicationTest SendPassword') .'@unknown123.kot';
        $account = \Stormpath\Resource\Account::instantiate(array(
            'givenName' => 'Account Name',
            'surname' => 'Surname',
            'username' => 'super_unique_username',
            'email' => $email,
            'password' => 'superP4ss'));
        self::$application->createAccount($account);
        $resetToken = self::$application->sendPasswordResetEmail($email, [], true);

        $this->assertInstanceOf('\Stormpath\Resource\PasswordResetToken', $resetToken);

        list($junk, $token) = explode('passwordResetTokens/',$resetToken->href);

        $password = 'A!a'.md5(uniqid());
        $doReset = self::$application->resetPassword($token, $password);

        $this->assertInstanceOf('\Stormpath\Resource\Account', $doReset);

        $authenticationRequest = new \Stormpath\Authc\UsernamePasswordRequest($email, $password);
        $result = self::$application->authenticateAccount($authenticationRequest);

        $this->assertInstanceOf('\Stormpath\Resource\Account', $result->account);
        $this->assertEquals($email, $result->account->email);

        $account->delete();
    }

    public function testCanVerifySPToken()
    {
        $application = \Stormpath\Resource\Application::instantiate(array('name' => makeUniqueName('ApplicationTest'), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, $application, array('createDirectory' => true));

        $email = makeUniqueName('ApplicationTest SendPassword') .'@unknown123.kot';
        $account = \Stormpath\Resource\Account::instantiate(array(
            'givenName' => 'Account Name',
            'surname' => 'Surname',
            'username' => 'super_unique_username',
            'email' => $email,
            'password' => 'superP4ss'));
        $thisAccount = $application->createAccount($account);
        $resetToken = $application->sendPasswordResetEmail($email, [], true);

        $this->assertInstanceOf('\Stormpath\Resource\PasswordResetToken', $resetToken);

        list($junk, $token) = explode('passwordResetTokens/',$resetToken->href);

        $account = $application->verifyPasswordResetToken($token);
        $this->assertInstanceOf('\Stormpath\Resource\Account', $account);

        $thisAccount->delete();
        $application->delete();
    }

    public function testSendVerificationEmail()
    {
        $application = self::$application;

        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('ApplicationTest testSendVerificationEmail')));
        self::createResource(\Stormpath\Resource\Directory::PATH, $directory);

        \Stormpath\Resource\AccountStoreMapping::create(
            array('accountStore' => $directory, 'application' => $application)
        );

        // set directory policy to enable verification email workflow
        $policy = $directory->accountCreationPolicy;
        $policy->verificationEmailStatus = 'ENABLED';
        $policy->save();
        $this->assertEquals('ENABLED', $policy->verificationEmailStatus);

        $username = makeUniqueName('ApplicationTest sendVerificaiton');
        $emailAddress = $username . '@unknown123.kot';
        $account = Account::instantiate(array(
            'givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => $username,
            'email' => $emailAddress,
            'password' => 'superP4ss'));

        $result = $directory->createAccount($account);
        $this->assertEquals($username, $result->username);
        $this->assertEquals($emailAddress, $result->email);




        try
        {
            $application->sendVerificationEmail($username);
        }
        catch(ResourceError $re)
        {
            $this->fail("Send verification email failed: ".$re->getErrorCode()." ".$re->getDeveloperMessage());
        }

        try
        {
            $application->sendVerificationEmail($username);
        }
        catch(ResourceError $re)
        {
            $this->fail("Send verification email failed: ".$re->getErrorCode()." ".$re->getDeveloperMessage());
        }

        try
        {
            $application->sendVerificationEmail($username, array('accountStore' => $directory));
        }
        catch(ResourceError $re)
        {
            $this->fail("Send verification email failed: ".$re->getErrorCode()." ".$re->getDeveloperMessage());
        }



        $directory->delete();
    }

    public function testAuthenticate()
    {
        $application = self::$application;
        $email = makeUniqueName('ApplicationTest testAuth') . '@unknown123.kot';

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => 'super_unique_username',
                                                                  'email' => $email,
                                                                  'password' => 'superP4ss'));

        $application->createAccount($account);

        $result = $application->authenticate($email, 'superP4ss');

        $this->assertEquals($email, $result->account->email);

        try {

            $application->authenticate($email, 'wrong_pass');
            $account->delete();
            $this->fail('Authentication should have failed.');

        } catch (\Stormpath\Resource\ResourceError $re)
        {
            $this->assertEquals(400, $re->getStatus());
            $this->assertEquals(7100, $re->getErrorCode());
            $this->assertContains('Invalid', $re->getMessage());
            $this->assertEquals('Login attempt failed because the specified password is incorrect.', $re->getDeveloperMessage());
            $this->assertContains('7100', $re->getMoreInfo());
        }

        $account->delete();
    }

    public function testAuthenticateWithAccountStore()
    {
        $application = self::$application;
        $email = makeUniqueName('ApplicationTest authWithAcctStore') . '@unknown123.kot';

        $groupA = new \stdClass();
        $groupA->name = makeUniqueName('ApplicationTest AuthWithAcctStore A');
        $groupA = \Stormpath\Resource\Group::instantiate($groupA);
        $application->createGroup($groupA);

        $groupB = new \stdClass();
        $groupB->name = makeUniqueName('ApplicationTest AuthWithAcctStore B');
        $groupB = \Stormpath\Resource\Group::instantiate($groupB);
        $application->createGroup($groupB);

        $accountStoreMappingA = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => $groupA));
        $application->createAccountStoreMapping($accountStoreMappingA);

        $accountStoreMappingB = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => $groupB));
        $application->createAccountStoreMapping($accountStoreMappingB);

        $account = \Stormpath\Resource\Account::instantiate(array(
            'givenName' => 'Account Name',
            'surname' => 'Surname',
            'username' => 'super_unique_username',
            'email' => $email,
            'password' => 'superP4ss'));

        $application->createAccount($account);
        $groupA->addAccount($account);

        $authenticationRequest = new \Stormpath\Authc\UsernamePasswordRequest(
            $email,
            'superP4ss',
            array('accountStore' => $accountStoreMappingA->getAccountStore()));
        $result = $application->authenticateAccount($authenticationRequest);
        $this->assertEquals($email, $result->account->email);

        try {
            $authenticationRequest = new \Stormpath\Authc\UsernamePasswordRequest(
                $email,
                'superP4ss',
                array('accountStore' => $accountStoreMappingB->getAccountStore()));
            $application->authenticateAccount($authenticationRequest);

            $account->delete();
            $accountStoreMappingB->delete();
            $accountStoreMappingA->delete();
            $groupB->delete();
            $groupA->delete();

            $this->fail('Authentication should have failed.');
        }
        catch (\Stormpath\Resource\ResourceError $re)
        {
            $this->assertEquals(400, $re->getStatus());
            $this->assertEquals(7104, $re->getErrorCode());
            $this->assertContains('Invalid', $re->getMessage());
            $this->assertEquals("Login attempt failed because there is no Account in the Application's associated Account Stores with the specified username or email.", $re->getDeveloperMessage());
            $this->assertContains('7104', $re->getMoreInfo());
            $this->assertNotNull($re->getRequestId());
        }

        try
        {
            new \Stormpath\Authc\UsernamePasswordRequest(
                $email,
                'superP4ss',
                array('accountStore' => 'not an instance of AccountStore'));

            $this->fail('UsernamePasswordRequest instantiation should have failed.');
        }
        catch (\InvalidArgumentException $iae)
        {
            $this->assertEquals("The value for accountStore in the \$options array should be an instance of \\Stormpath\\Resource\\AccountStore", $iae->getMessage());
        }
        catch (\Exception $e)
        {
            $this->fail('UsernamePasswordRequest instantiation with wrong type for account store should have thrown InvalidArgumentException.');
        }

        $account->delete();
        $accountStoreMappingB->delete();
        $accountStoreMappingA->delete();
        $groupB->delete();
        $groupA->delete();
    }

    public function testSave()
    {
        $application = self::$application;

        $application->name = makeUniqueName('ApplicationTest testSave');
        $application->description = 'Description of Main App changed';
        $application->status = 'disabled';

        $application->save();

        $this->assertContains('ApplicationTest_testSave', $application->name);
        $this->assertContains('Description of Main App changed', $application->description);
        $this->assertEquals('DISABLED', $application->status);

        $application->status = 'enabled';

        $application->save();
    }

    public function testApiKeyManagement()
    {
        $application = self::$application;

        $account = \Stormpath\Resource\Account::instantiate(array(
            'givenName' => 'Account Name',
            'surname' => 'Surname',
            'username' => makeUniqueName('ApplicationTest ApiKeyMgt'),
            'email' => makeUniqueName('ApplicationTest ApiKeyMgt') .'@unknown123.kot',
            'password' => 'superP4ss'));

        $application->createAccount($account);
        $account = \Stormpath\Resource\Account::get($account->href);

        $newApiKey = $account->createApiKey();
        $this->assertNotEmpty($newApiKey->id);

        $apiKey = $application->getApiKey($newApiKey->id);
        $this->assertEquals($newApiKey, $apiKey);

        $encryptedApiKey = $application->getApiKey($newApiKey->id,
            array('encryptSecret' => true));

        $this->assertEquals($apiKey->secret, $encryptedApiKey->secret);

        $apiKey->status = 'DISABLED';
        $apiKey->save();
        $this->assertEquals('DISABLED', $apiKey->status);

        $apiKey->delete();
        $apiKey = $application->getApiKey($newApiKey->id);
        $this->assertNull($apiKey);

        $account->delete();
    }

    public function testOauthPolicy()
    {
        $policy = self::$application->oauthPolicy;
        $accessTokenTtl = $policy->accessTokenTtl;
        $refreshTokenTtl = $policy->refreshTokenTtl;

        $this->assertNotNull($accessTokenTtl);
        $this->assertNotNull($refreshTokenTtl);

        $policy->accessTokenTtl = 'PT1M';
        $policy->refreshTokenTtl = 'PT1M';
        $policy->save();

        $policy = self::$application->oauthPolicy;
        $accessTokenTtl = $policy->accessTokenTtl;
        $refreshTokenTtl = $policy->refreshTokenTtl;
        $this->assertEquals('PT1M', $accessTokenTtl);
        $this->assertEquals('PT1M', $refreshTokenTtl);
    }

    public function testAddingCustomData()
    {
        $cd = self::$application->customData;

        $cd->unitTest = "unit Test";
        $cd->save();

        $application = \Stormpath\Resource\Application::get(self::$application->href);
        $customData = $application->customData;
        $this->assertEquals('unit Test', $customData->unitTest);



    }

    public function testUpdatingCustomData()
    {
        $cd = self::$application->customData;

        $cd->unitTest = "some change";
        $cd->save();

        $application = \Stormpath\Resource\Application::get(self::$application->href);
        $customData = $application->customData;
        $this->assertEquals('some change', $customData->unitTest);

    }

    public function testRemovingCustomData()
    {
        $cd = self::$application->customData;

        $cd->remove('unitTest');

        $newClient = self::newClientInstance();
        $application = $newClient->dataStore->getResource(self::$application->href, Stormpath::APPLICATION);
        $customData = $application->customData;
        $this->assertNull($customData->unitTest);
    }

    public function testDeletingAllCustomData()
    {
        $cd = self::$application->customData;
        $cd->unitTest = "some change";
        $cd->rank = "Captain";
        $cd->birthDate = "2305-07-13";
        $cd->favoriteDrink = "favoriteDrink";
        $cd->save();

        $cd->delete();

        $newClient = self::newClientInstance();
        $application = $newClient->dataStore->getResource(self::$application->href, Stormpath::APPLICATION);
        $customData = $application->customData;
        $this->assertNull($customData->unitTest);
        $this->assertNull($customData->rank);
        $this->assertNull($customData->birthDate);
        $this->assertNull($customData->favoriteDrink);
    }

    public function testShouldBeAbleToGetApplicationViaHTMLFragment()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => makeUniqueName('ApplicationTest testFragment')));

        $href = $application->href;

        $hrefParts = array_reverse(explode('/',$href));

        $app = \Stormpath\Resource\Application::get($hrefParts[0]);

        $this->assertInstanceOf('\Stormpath\Resource\Application', $app);
        $this->assertEquals($href, $app->href);

        $app2 = \Stormpath\Client::get($hrefParts[1].'/'.$hrefParts[0], Stormpath::APPLICATION);

        $this->assertInstanceOf('\Stormpath\Resource\Application', $app2);
        $this->assertEquals($href, $app2->href);

        $application->delete();
    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testDelete()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => makeUniqueName('ApplicationTest testDelete')));

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('testDelete', $application->name);

        $href = $application->href;
        $application->delete();

        \Stormpath\Resource\Application::get($href);
    }
    
    /** @test */
    public function an_application_should_allow_setting_authorized_callback_uri()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => makeUniqueName('ApplicationTest authorizedCallbackUri')));

        $application->setAuthorizedCallbackUris([
            'http://myapplication.com/whatever/callback',
            'http://myapplication.com/whatever/callback2'
        ]);

        $application->save();

        $application = \Stormpath\Resource\Application::get($application->href);

        $this->assertCount(2, $application->authorizedCallbackUris);

        $application->delete();
    }
    





}
