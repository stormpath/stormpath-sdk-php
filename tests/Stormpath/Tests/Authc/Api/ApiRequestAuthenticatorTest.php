<?php

namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\ApiRequestAuthenticator;
use Stormpath\Authc\Api\Request;
use Stormpath\Tests\BaseTest;

class ApiRequestAuthenticatorTest extends BaseTest
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
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only Basic Authorization headers are accepted at this time.
     */
    public function it_throws_exception_if_scheme_is_not_basic()
    {
        $account = $this->createAccount();
        $apiKey = $account->createApiKey();

        $key = base64_encode($apiKey->getId().':'.$apiKey->getSecret());

        $_REQUEST['Authorization'] = "SOMETHING $key";
        $request = Request::createFromGlobals();

        $account->delete();

        $auth = (new ApiRequestAuthenticator(self::$application))->authenticate($request);

        Request::tearDown();


    }

    /**
     * @test
     */
    public function it_returns_the_correct_return_type()
    {
        $account = $this->createAccount();
        $apiKey = $account->createApiKey();

        $key = base64_encode($apiKey->getId().':'.$apiKey->getSecret());

        $_REQUEST['Authorization'] = "Basic $key";
        $request = Request::createFromGlobals();

        $auth = (new ApiRequestAuthenticator(self::$application))->authenticate($request);

        $this->assertInstanceOf('Stormpath\Authc\Api\BasicAuthenticationResult', $auth);

        $this->assertInstanceOf('Stormpath\Resource\Application', $auth->getApplication());
        $this->assertInstanceOf('Stormpath\Resource\ApiKey', $auth->getApiKey());

        $this->assertEquals($apiKey->getId(), $auth->getApiKey()->getId());
        $this->assertEquals($apiKey->getSecret(), $auth->getApiKey()->getSecret());
        $this->assertEquals(self::$application->href, $auth->getApplication()->getHref());

        $account->delete();
    }

    private function createAccount()
    {

        $this->account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => md5(time()) . 'username',
            'email' => md5(time()) .'@unknown123.kot',
            'password' => 'superP4ss'));

        self::$application->createAccount($this->account);
        return $this->account;
    }

    public function tearDown()
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