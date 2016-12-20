<?php
namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\OAuthPasswordRequestAuthenticator;
use Stormpath\Authc\Api\OAuthRequestAuthenticator;
use Stormpath\Authc\Api\Request;
use Stormpath\Tests\TestCase;

class OAuthRequestAuthenticatorTest extends TestCase
{

    public static $account;

    private static $application;

    private static $apiKey;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = \Stormpath\Resource\Application::instantiate(
            array(
                'name' => makeUniqueName('Application OauthRequestAuthenticatorTest'),
                'description' => 'Application for OAuthRequestAuthenticatorTest',
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
                'middleName' => 'OAuthRequestAuthenticator',
                'surname' => 'Test',
                'username' => makeUniqueName('OAuthRequestAuthenticatorTest'),
                'email' => makeUniqueName('OAuthRequestAuthenticatorTest') .'@testmail.stormpath.com',
                'password' => 'superP4ss'

            )
        );
        self::$application->createAccount(self::$account);

        self::$apiKey = self::$account->createApiKey();

    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\RequestAuthenticatorException
     * @expectedExceptionMessage Authentication: OAuth Bearer, or OAuth Client Credentials.
     */
    public function it_throws_exception_if_oauth_request_is_not_made()
    {
        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['QUERY_STRING'] = '';

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthRequestAuthenticator(self::$application);
        $result = $auth->authenticate(Request::createFromGlobals());
    }
    
    
    /**
     * @test
     */
    public function it_authorizes_with_client_credentials_request()
    {
        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = 'http://test.com/?grant_type=client_credentials';
        $_SERVER['QUERY_STRING'] = 'grant_type=client_credentials';

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthRequestAuthenticator(self::$application);
        $result = $auth->authenticate(Request::createFromGlobals());
        $token = json_decode($result->getAccessToken());

        $this->assertInstanceOf('Stormpath\Authc\Api\OAuthAuthenticationResult', $result);

        $this->assertInstanceOf('Stormpath\Resource\Application', $result->getApplication());
        $this->assertInstanceOf('Stormpath\Resource\ApiKey', $result->getApiKey());
        $this->assertObjectHasAttribute('access_token', $token);
        $this->assertObjectHasAttribute('token_type', $token);
        $this->assertObjectHasAttribute('expires_in', $token);
    }

    /**
     * @test
     */
    public function it_authorizes_with_bearer_token()
    {
        $authorization = 'Basic ' . base64_encode(self::$apiKey->id . ':' . self::$apiKey->secret);
        $_SERVER['HTTP_AUTHORIZATION'] = $authorization;
        $_SERVER['REQUEST_URI'] = 'http://test.com/?grant_type=client_credentials';
        $_SERVER['QUERY_STRING'] = 'grant_type=client_credentials';

        self::$apiKey->setStatus('ENABLED');
        self::$apiKey->save();

        self::$account->setStatus('ENABLED');
        self::$account->save();

        $auth = new OAuthRequestAuthenticator(self::$application);
        $result = $auth->authenticate(Request::createFromGlobals());
        $token = json_decode($result->getAccessToken());
        $accessToken = $token->access_token;

        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer $accessToken";
        $auth = new OAuthRequestAuthenticator(self::$application);
        $result = $auth->authenticate(Request::createFromGlobals());

        $this->assertInstanceOf('Stormpath\Authc\Api\OAuthAuthenticationResult', $result);

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