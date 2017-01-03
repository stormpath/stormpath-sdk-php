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

use Stormpath\Client;

class PasswordRefreshGrantTest extends \Stormpath\Tests\TestCase
{
	/**
	 * @var \Stormpath\Resource\Application
	 */
	private static $application;
    private static $account;
    private static $inited;


	/**
	 * @var \Stormpath\Resource\Organization
	 */
	private static $org;

	protected static function init()
    {
        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for the tests' .md5(time().microtime().uniqid()), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => md5(time().microtime().uniqid()) . 'username',
            'email' => md5(time().microtime().uniqid()) .'@testmail.stormpath.com',
            'password' => 'superP4ss'));

        self::$application->createAccount(self::$account);

	    // add org
	    $org = \Stormpath\Resource\Organization::instantiate([
		    'name' => makeUniqueName('Org'),
		    'nameKey' => 'org-'.time(),
		    'status' => \Stormpath\Stormpath::ENABLED
	    ]);

	    self::$org = Client::getInstance()->getTenant()->createOrganization($org);

	    // attach directory to org
	    $asm = \Stormpath\Resource\AccountStoreMapping::instantiate([
		    'organization' => self::$org,
		    'accountStore' =>self::$application->getAccountStoreMappings()->getIterator()->current()->getAccountStore()
	    ]);

	    self::$org->createOrganizationAccountStoreMapping($asm);

	    $asm = \Stormpath\Resource\AccountStoreMapping::instantiate([
		    'application' => self::$application,
		    'accountStore' => self::$org
	    ]);

	    self::$application->createAccountStoreMapping($asm);


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
	    if (self::$org) {
		    self::$org->delete();
	    }

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

    /**
     * @test
     */
    public function it_responds_to_password_grant_types()
    {
        $passwordGrant = new \Stormpath\Oauth\PasswordGrantRequest(self::$account->username, 'superP4ss');

        $auth = new \Stormpath\Oauth\PasswordGrantAuthenticator(self::$application);
        $token = $auth->authenticate($passwordGrant);

        $this->assertInstanceOf('Stormpath\Oauth\OauthGrantAuthenticationResult', $token);
        $this->assertInstanceOf('Stormpath\Resource\AccessToken', $token->getAccessToken());
        $this->assertCount(3, explode('.',$token->getAccessTokenString()));
        $this->assertInstanceOf('Stormpath\Resource\RefreshToken', $token->getRefreshToken());
        $this->assertCount(3, explode('.',$token->getRefreshTokenString()));
        $this->assertcontains('/accessTokens/', $token->getAccessTokenHref());
        $this->assertEquals('Bearer', $token->getTokenType());
        $this->assertTrue(is_integer($token->getExpiresIn()));
    }

	/**
	 * @test
	 */
	public function supplying_org_name_key_returns_organization_claim()
	{
		$passwordGrant = new \Stormpath\Oauth\PasswordGrantRequest(self::$account->username, 'superP4ss', self::$org->nameKey);

		$auth = new \Stormpath\Oauth\PasswordGrantAuthenticator(self::$application);
		$token = $auth->authenticate($passwordGrant);

		$this->assertInstanceOf('Stormpath\Oauth\OauthGrantAuthenticationResult', $token);
		$this->assertInstanceOf('Stormpath\Resource\AccessToken', $token->getAccessToken());
		$this->assertCount(3, explode('.',$token->getAccessTokenString()));
		$this->assertInstanceOf('Stormpath\Resource\RefreshToken', $token->getRefreshToken());
		$this->assertCount(3, explode('.',$token->getRefreshTokenString()));
		$this->assertcontains('/accessTokens/', $token->getAccessTokenHref());
		$this->assertEquals('Bearer', $token->getTokenType());
		$this->assertTrue(is_integer($token->getExpiresIn()));

		// Open Access Token to see if org exists
		$decoded = \Firebase\JWT\JWT::decode($token->getAccessTokenString(), Client::getInstance()->getDataStore()->getApiKey()->getSecret(), ['HS256']);

		$this->assertTrue(property_exists($decoded, 'org'));
		if(property_exists($decoded, 'org')) {
			$this->assertEquals(self::$org->href, $decoded->org);
		}

	}

    /**
     * @test
     */
    public function it_responds_to_refresh_grant_types()
    {
        $passwordGrant = new \Stormpath\Oauth\PasswordGrantRequest(self::$account->username, 'superP4ss');

        $auth = new \Stormpath\Oauth\PasswordGrantAuthenticator(self::$application);
        $token = $auth->authenticate($passwordGrant);

        $refreshGrant = new \Stormpath\Oauth\RefreshGrantRequest($token->getRefreshTokenString());

        $auth = new \Stormpath\Oauth\RefreshGrantAuthenticator(self::$application);
        $result = $auth->authenticate($refreshGrant);

        $this->assertInstanceOf('Stormpath\Oauth\OauthGrantAuthenticationResult', $result);
        $this->assertInstanceOf('Stormpath\Resource\AccessToken', $result->getAccessToken());
        $this->assertCount(3, explode('.',$result->getAccessTokenString()));
        $this->assertInstanceOf('Stormpath\Resource\RefreshToken', $result->getRefreshToken());
        $this->assertCount(3, explode('.',$result->getRefreshTokenString()));
        $this->assertcontains('/accessTokens/', $result->getAccessTokenHref());
        $this->assertEquals('Bearer', $result->getTokenType());
        $this->assertTrue(is_integer($result->getExpiresIn()));
    }

}
