<?php
namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\OAuthPasswordRequestAuthenticator;
use Stormpath\Tests\TestCase;

class OAuthPasswordAuthenticationResult extends TestCase
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
                'email' => md5(time() . microtime() . uniqid()) . '@unknown123.kot',
                'password' => 'superP4ss'

            )
        );
        self::$application->createAccount(self::$account);
    }


    /**
     * @test
     */
    public function it_returns_oauth_token_resource_on_successful_authentication()
    {
        $auth = new OAuthPasswordRequestAuthenticator(self::$application);
        $tokens = $auth->authenticate(self::$account->username, 'superP4ss');
        var_dump($tokens);
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