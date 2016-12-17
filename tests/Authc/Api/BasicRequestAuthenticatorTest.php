<?php
namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\BasicRequestAuthenticator;
use Stormpath\Authc\Api\Request;
use Stormpath\Tests\TestCase;

class BasicRequestAuthenticatorTest extends TestCase
{

    public static $account;

    private static $application;

    private static $apiKey;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = \Stormpath\Resource\Application::instantiate(
            array(
                'name' => makeUniqueName('Application BasicRequestAuthenticatorTest'),
                'description' => 'Application for BasicRequestAuthenticatorTest',
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
                'middleName' => 'BasicRequestAuthenticatorTest',
                'surname' => 'Test',
                'username' => makeUniqueName('BasicRequestAuthenticatorTest'),
                'email' => makeUniqueName('BasicRequestAuthenticatorTest') .'@testmail.stormpath.com',
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

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new BasicRequestAuthenticator(self::$application);
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

        self::$apiKey->setStatus('DISABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new BasicRequestAuthenticator(self::$application);
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

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('DISABLED');
        self::$account->save();

        $auth = new BasicRequestAuthenticator(self::$application);
        $auth->authenticate(Request::createFromGlobals());
    }

    /**
     * @test
     */
    public function it_can_authenticate_a_basic_request()
    {
        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new BasicRequestAuthenticator(self::$application);
        $result = $auth->authenticate(Request::createFromGlobals());

        $this->assertInstanceOf('Stormpath\Authc\Api\BasicAuthenticationResult', $result);

        $this->assertInstanceOf('Stormpath\Resource\Application', $result->getApplication());
        $this->assertInstanceOf('Stormpath\Resource\ApiKey', $result->getApiKey());

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