<?php
namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\OAuthBearerAuthenticationResult;
use Stormpath\Authc\Api\OAuthBearerRequestAuthenticator;
use Stormpath\Authc\Api\OAuthClientCredentialsRequestAuthenticator;
use Stormpath\Authc\Api\Request;
use Stormpath\Tests\BaseTest;

class OAuthBearerRequestAuthenticatorTest extends BaseTest
{

    public static $account;

    private static $application;

    private static $apiKey;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = \Stormpath\Resource\Application::instantiate(
            array(
                'name' => 'Main App for the tests' .md5(time()),
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
                'username' => md5(time().microtime().uniqid()) . 'username',
                'email' => md5(time().microtime().uniqid()) .'@unknown123.kot',
                'password' => 'superP4ss'

            )
        );
        self::$application->createAccount(self::$account);

        self::$apiKey = self::$account->createApiKey();

    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\RequestAuthenticatorException
     * @expectedExceptionMessage The API Key is not allowed to make this request.
     */
    public function it_throws_exception_if_the_api_key_is_disabled()
    {

        $accessToken = $this->getAccessToken();
        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer $accessToken";

        self::$apiKey->setStatus('DISABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthBearerRequestAuthenticator(self::$application);
        $auth->authenticate(Request::createFromGlobals());


    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\RequestAuthenticatorException
     * @expectedExceptionMessage The Account you are authenticating with is not active.
     */
    public function it_throws_exception_if_the_account_is_disabled()
    {
        $accessToken = $this->getAccessToken();

        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer $accessToken";

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('DISABLED');
        self::$account->save();

        $auth = new OAuthBearerRequestAuthenticator(self::$application);
        $auth->authenticate(Request::createFromGlobals());
    }

    /**
     * @test
     */
    public function it_can_authenticate_a_valid_bearer_request()
    {
        $accessToken = $this->getAccessToken();

        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer $accessToken";
        $auth = new OAuthBearerRequestAuthenticator(self::$application);
        $result = $auth->authenticate(Request::createFromGlobals());

        $this->assertInstanceOf('Stormpath\Authc\Api\OAuthBearerAuthenticationResult', $result);

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

    /**
     * @return array
     */
    private function getAccessToken()
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
        $accessToken = $token->access_token;

        Request::tearDown();
        return $accessToken;
    }


}