<?php 

namespace StormpathTest\Resource;

use PHPUnit_Framework_TestCase;
use Stormpath\Resource\Account;
use Stormpath\Resource\Group;
use Stormpath\Resource\Application;
use Stormpath\Persistence\ResourceManager;
use Stormpath\Service\StormpathService;

class AccountTest extends \PHPUnit_Framework_TestCase
{
    protected $account;
    protected $application;
    protected $group;

    protected function setUp()
    {
        $resourceManager = StormpathService::getResourceManager();

        $app = new Application;

        $app->setName(md5(rand()));
        $app->setDescription('phpunit test application');
        $app->setStatus('ENABLED');

        $resourceManager->persist($app);
        $resourceManager->flush();

        $this->application = $app;
    }

    protected function tearDown()
    {
        $resourceManager = StormpathService::getResourceManager();
        $resourceManager->remove($this->application);
        $resourceManager->flush();
    }

    public function testAccountEmailAndPassword()
    {

        $expectedEmailError = 'Invalid email address';
        $expectedPasswordError = 'Password must be mixed case';
        
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', $expectedEmailError);
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', $expectedPasswordError);

        $resourceManager = StormpathService::getResourceManager();

        $username = md5(rand());
        $password = 'soliant';
        $email = md5(rand()) . '@test.stormpath.';

        $account = new Account;
        $account->setUsername($username);
        $account->setEmail($email);
        $account->setPassword($password);
        $account->setGivenName('Test');
        $account->setMiddleName('User');
        $account->setSurname('One');
        $account->setApplication($this->application);
        $account->setStatus('ENABLED');

        $resourceManager->persist($account);
        $resourceManager->flush();
        
        $this->assertEquals($email, $account->getEmail());
        $this->assertEquals($password, $account->getPassword());
    }
}