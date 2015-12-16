<?php


class PasswordRefreshGrantTest extends \Stormpath\Tests\TestCase
{
    private static $application;
    private static $account;
    private static $inited;
    private static $token;

    protected static function init()
    {
        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for the tests' .md5(time().microtime().uniqid()), 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => md5(time().microtime().uniqid()) . 'username',
            'email' => md5(time().microtime().uniqid()) .'@unknown123.kot',
            'password' => 'superP4ss'));

        self::$application->createAccount(self::$account);

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

    /**
     * @test
     */
    public function it_responds_to_password_grant_types()
    {
        $passwordGrant = new \Stormpath\Oauth\PasswordGrantRequest(self::$account->username, 'superP4ss');

        $auth = new \Stormpath\Oauth\PasswordGrantAuthenticator(self::$application);
        self::$token = $auth->authenticate($passwordGrant);

        $this->assertInstanceOf('Stormpath\Oauth\OauthGrantAuthenticationResult', self::$token);
        $this->assertInstanceOf('Stormpath\Resource\AccessToken', self::$token->getAccessToken());
        $this->assertCount(3, explode('.',self::$token->getAccessTokenString()));
        $this->assertNull(self::$token->getRefreshToken());
        $this->assertCount(3, explode('.',self::$token->getRefreshTokenString()));
        $this->assertcontains('/accessTokens/', self::$token->getAccessTokenHref());
        $this->assertEquals('Bearer', self::$token->getTokenType());
        $this->assertTrue(is_integer(self::$token->getExpiresIn()));
    }

    /**
     * @test
     */
    public function it_responds_to_refresh_grant_types()
    {
        $refreshGrant = new \Stormpath\Oauth\RefreshGrantRequest(self::$token->getRefreshTokenString());

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
