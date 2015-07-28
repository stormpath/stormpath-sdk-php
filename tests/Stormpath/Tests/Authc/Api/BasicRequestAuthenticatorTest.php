<?php

namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\BasicRequestAuthenticator;
use Stormpath\Authc\Api\BasicAuthenticationResult;
use Stormpath\Authc\Api\Request;
use Stormpath\Stormpath;
use Stormpath\Tests\BaseTest;

class BasicRequestAuthenticatorTest extends BaseTest
{
    public static $request;
    public static $application;
    public $account;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => 'Main App for the tests' .md5(time()), 'description' => 'Description of Main App', 'status' => 'enabled'));
        parent::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

    }



    /**
     * @test
     * @expectedException Stormpath\Exceptions\AuthenticatorException
     * @expectedExceptionMessage The API Key is not valid for this request.
     */
    public function it_throws_exception_for_invalid_api_key()
    {
        $request = $this->setUpAuthHeader('123:abc');
        $auth = new BasicRequestAuthenticator(self::$application);
        $auth->authenticate($request);

    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\AuthenticatorException
     * @expectedExceptionMessage The API Key is not allowed to make this request.
     */
    public function it_throws_exception_for_disabled_api_key()
    {
        $account = $this->createAccount();
        $apiKey = $account->createApiKey();
        $apiKey->setStatus('DISABLED');
        $apiKey->save();


        $request = $this->setUpAuthHeader($apiKey->getId().':'.$apiKey->getSecret());
        $auth = new BasicRequestAuthenticator(self::$application);
            $auth->authenticate($request);

        $this->deleteAccount();
    }

    /**
     * @test
     * @expectedException Stormpath\Exceptions\AuthenticatorException
     * @expectedExceptionMessage The Account you are authenticating with is not active.
     */
    public function it_throws_exception_for_disabled_account()
    {
        $account = $this->createAccount();
        $apiKey = $account->createApiKey();
        $account->setStatus('DISABLED');
        $account->save();


        $request = $this->setUpAuthHeader($apiKey->getId().':'.$apiKey->getSecret());
        $auth = new BasicRequestAuthenticator(self::$application);
        $auth->authenticate($request);

        $this->deleteAccount();
    }

    /**
     * @test
    */
    public function it_authenticated_a_valid_user_with_api_key_via_basic_auth()
    {
        $account = $this->createAccount();
        $apiKey = $account->createApiKey();

        $request = $this->setUpAuthHeader($apiKey->getId().':'.$apiKey->getSecret());
        $auth = new BasicRequestAuthenticator(self::$application);
        $result = $auth->authenticate($request);

        $this->assertInstanceOf('Stormpath\Authc\Api\BasicAuthenticationResult', $auth);

        $this->assertInstanceOf('Stormpath\Resource\Application',  $result->getApplication());
        $this->assertInstanceOf('Stormpath\Resource\ApiKey', $result->getApiKey());

        $this->assertEquals($apiKey->getId(), $result->getApiKey()->getId());
        $this->assertEquals($apiKey->getSecret(), $result->getApiKey()->getSecret());
        $this->assertEquals(self::$application->href, $result->getApplication()->getHref());

        $this->deleteAccount();
    }

    public function tearDown()
    {
        $this->tearDownAuthHeader();
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

    private function setUpAuthHeader($auth)
    {
        $authHeader = base64_encode($auth);
        $_REQUEST['Authorization'] = "Basic $authHeader";
        return Request::createFromGlobals();
    }

    private function tearDownAuthHeader()
    {
        unset($_REQUEST['Authorization']);
        Request::tearDown();
    }

    private function createAccount()
    {

        $this->account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => md5(time().microtime().uniqid()) . 'username',
            'email' => md5(time().microtime().uniqid()) .'@unknown123.kot',
            'password' => 'superP4ss'));

        self::$application->createAccount($this->account);
        return $this->account;
    }

    private function deleteAccount()
    {
        $this->account->delete();

        $this->account = null;
    }




}
