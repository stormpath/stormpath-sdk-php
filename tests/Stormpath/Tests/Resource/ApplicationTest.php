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


use JWT;
use Stormpath\Client;
use Stormpath\Resource\Application;
use Stormpath\Util\UUID;

class ApplicationTest extends \Stormpath\Tests\BaseTest {

    private static $application;
    private static $inited;

    private static $directory;
    private static $account;

    protected static function init()
    {
        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for the tests' .md5(time()), 'description' => 'Description of Main App', 'status' => 'enabled'));
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
    }

    public function testGet()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Main App', $application->name);
    }

    public function testCreateIdSiteURLReturnsURLWithJWT()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $redirectUrl = $application->createIdSiteUrl(array(
            'callbackUri' => 'https://stormpath.com',
            'state' => UUID::v4()
        ));

        $this->assertContains('https://api.stormpath.com/sso?jwtRequest=', $redirectUrl);
    }


    public function testCreateIdSiteUrlReturnsCorrectPathForLogoutRequest()
    {
        $application = \Stormpath\Resource\Application::get(self::$application->href);

        $redirectUrl = $application->createIdSiteUrl(array(
            'callbackUri' => 'https://stormpath.com',
            'logout'=>true,
            'state' => UUID::v4()
        ));

        $this->assertContains('https://api.stormpath.com/sso/logout?jwtRequest=', $redirectUrl);
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

    protected function createAccount()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => md5(time())));

        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => md5(time()) . 'username',
            'email' => md5(time()) .'@unknown123.kot',
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
        $application = \Stormpath\Resource\Application::create(array('name' => 'Another App'. md5(time())));

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Another App', $application->name);

        // testing the creation from an application instance
        $application2 = \Stormpath\Resource\Application::instantiate(array('name' => 'Yet another App'. md5(time())));
        \Stormpath\Resource\Application::create($application2);

        $this->assertInstanceOf('Stormpath\Resource\Application', $application2);
        $this->assertContains('Yet another App', $application2->name);

        $application->delete();
        $application2->delete();
    }

    public function testGetters()
    {
        $application = self::$application;

        $this->assertContains('Main App', $application->name);
        $this->assertContains('Description of Main App', $application->description);
        $this->assertEquals(self::$client->tenant->name, $application->tenant->name);
        $this->assertInstanceOf('Stormpath\Resource\AccountList', $application->accounts);
        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $application->defaultAccountStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $application->defaultGroupStoreMapping);
        $this->assertInstanceOf('Stormpath\Resource\GroupList', $application->groups);
        $this->assertInstanceOf('Stormpath\Resource\AccountStoreMappingList', $application->accountStoreMappings);
        $this->assertInstanceOf('Stormpath\Resource\BasicLoginAttempt', $application->loginAttempts);

        foreach($application->accountStoreMappings as $acm)
        {
            $this->assertInstanceOf('Stormpath\Resource\AccountStoreMapping', $acm);
            $this->assertContains('Main App', $acm->accountStore->name);
        }
    }

    public function testCreateAccount()
    {
        $application = self::$application;

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => md5(time()) . 'username',
                                                                  'email' => md5(time()) .'@unknown123.kot',
                                                                  'password' => 'superP4ss'));

        $application->createAccount($account);

        $account = \Stormpath\Resource\Account::get($account->href);

        $this->assertEquals('Account Name', $account->givenName);

        $account->delete();
    }

    public function testCreateGroup()
    {
        $application = self::$application;

        $group = new \stdClass();
        $group->name = 'New Group in town'.md5(time());

        $group = \Stormpath\Resource\Group::instantiate($group);
        $application->createGroup($group);

        $group = \Stormpath\Resource\Group::get($group->href);

        $this->assertContains('New Group in town', $group->name);

        $group->delete();
    }

    public function testCreateAccountStoreMapping()
    {
        $application = self::$application;

        $directory = \Stormpath\Resource\Directory::create(array('name' => 'New Account Store in town'.md5(time())));

        $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::instantiate(array('accountStore' => $directory));

        $application->createAccountStoreMapping($accountStoreMapping);

        $this->assertContains('New Account Store in town', $accountStoreMapping->accountStore->name);

        $accountStoreMapping->delete();
        $directory->delete();
    }

    public function testSendPasswordResetEmail()
    {
        $application = self::$application;

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => 'super_unique_username',
                                                                  'email' => 'super_unique_email@unknown123.kot',
                                                                  'password' => 'superP4ss'));

        $application->createAccount($account);

        $account = $application->sendPasswordResetEmail('super_unique_email@unknown123.kot');

        $this->assertEquals('super_unique_email@unknown123.kot', $account->email);

        $account->delete();
    }

    public function testAuthenticate()
    {
        $application = self::$application;

        $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => 'super_unique_username',
                                                                  'email' => 'super_dupper_unique_email@unknown123.kot',
                                                                  'password' => 'superP4ss'));

        $application->createAccount($account);

        $result = $application->authenticate('super_dupper_unique_email@unknown123.kot', 'superP4ss');

        $this->assertEquals('super_dupper_unique_email@unknown123.kot', $result->account->email);

        try {

            $application->authenticate('super_dupper_unique_email@unknown123.kot', 'wrong_pass');
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

    public function testSave()
    {
        $application = self::$application;

        $application->name = 'Main App for the tests changed' .md5(time());
        $application->description = 'Description of Main App changed';
        $application->status = 'disabled';

        $application->save();

        $this->assertContains('Main App for the tests changed', $application->name);
        $this->assertContains('Description of Main App changed', $application->description);
        $this->assertEquals('DISABLED', $application->status);

        $application->status = 'enabled';

        $application->save();
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

        $application = \Stormpath\Resource\Application::get(self::$application->href);
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

        $application = \Stormpath\Resource\Application::get(self::$application->href);
        $customData = $application->customData;
        $this->assertNull($customData->unitTest);
        $this->assertNull($customData->rank);
        $this->assertNull($customData->birthDate);
        $this->assertNull($customData->favoriteDrink);
    }

    /**
     * @expectedException \Stormpath\Resource\ResourceError
     */
    public function testDelete()
    {
        $application = \Stormpath\Resource\Application::create(array('name' => 'Yet Another App'. md5(time())));

        $this->assertInstanceOf('Stormpath\Resource\Application', $application);
        $this->assertContains('Yet Another App', $application->name);

        $href = $application->href;
        $application->delete();

        \Stormpath\Resource\Application::get($href);
    }



}