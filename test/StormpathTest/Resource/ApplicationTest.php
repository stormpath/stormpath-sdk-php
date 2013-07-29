<?php

namespace StormpathTest\Resource;

use PHPUnit_Framework_TestCase;
use Stormpath\Service\StormpathService;
use Stormpath\Resource\Application;
use Stormpath\Resource\Account;
use Stormpath\Resource\PasswordResetToken;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected $application;

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

    public function testUpdate()
    {
        $resourceManager = StormpathService::getResourceManager();

        $originalDescription = $this->application->getDescription();

        $newDescription = md5(rand());
        $this->application->setDescription($newDescription);
        $resourceManager->persist($this->application);
        $resourceManager->flush();

        $resourceManager->refresh($this->application);

        $this->assertEquals($newDescription, $this->application->getDescription());

        $this->application->setDescription($originalDescription);
        $resourceManager->persist($this->application);
        $resourceManager->flush();
    }


    public function testLoginAttempt()
    {
        $resourceManager = StormpathService::getResourceManager();

        $username = md5(rand());
        $password = md5(rand()) . strtoupper(md5(rand()));
        $email = md5(rand()) . '@test.stormpath.com';

        $account1 = new Account;
        $account1->setUsername($username);
        $account1->setEmail($email);
        $account1->setPassword($password);
        $account1->setGivenName('Test');
        $account1->setMiddleName('User');
        $account1->setSurname('One');
        $account1->setApplication($this->application);
        $account1->setStatus('ENABLED');

        $resourceManager->persist($account1);
        $resourceManager->flush();

        // Test login attempt
        $loginAttempt = new LoginAttempt;
        $loginAttempt->setUsername($email);
        $loginAttempt->setPassword($password);
        $loginAttempt->setApplication($this->app);

        $resourceManager->persist($loginAttempt);
        $resourceManager->flush();

        $this->assertTrue($loginAttempt->getAccount() instanceof Account);
        $this->assertEquals($loginAttempt->getAccount()->getId(), $account1->getId());

        $resourceManager->remove($account1);
        $resourceManager->flush();
    }

}

