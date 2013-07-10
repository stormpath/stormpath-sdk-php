<?php

namespace StormpathTest\Resource;

use PHPUnit_Framework_TestCase;
use Stormpath\Service\StormpathService;
use Stormpath\Resource\Directory;
use Stormpath\Resource\Account;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    protected $directory;

    protected function setUp()
    {
        $resourceManager = StormpathService::getResourceManager();

        $dir = new Directory;

        $dir->setName(md5(rand()));
        $dir->setDescription('phpunit test directory');
        $dir->setStatus('ENABLED');

        $resourceManager->persist($dir);
        $resourceManager->flush();

        $this->directory = $dir;
    }

    protected function tearDown()
    {
        $resourceManager = StormpathService::getResourceManager();
        $resourceManager->remove($this->directory);
        $resourceManager->flush();
    }

    public function testUpdate()
    {
        $resourceManager = StormpathService::getResourceManager();

        $originalDescription = $this->directory->getDescription();

        $newDescription = md5(rand());
        $this->directory->setDescription($newDescription);
        $resourceManager->persist($this->directory);
        $resourceManager->flush();

        $resourceManager->refresh($this->directory);

        $this->assertEquals($newDescription, $this->directory->getDescription());

        $this->directory->setDescription($originalDescription);
        $resourceManager->persist($this->directory);
        $resourceManager->flush();
    }

    public function testAddAccounts()
    {
        $resourceManager = StormpathService::getResourceManager();

        $account1 = new Account;
        $account1->setUsername(md5(rand()));
        $account1->setEmail(md5(rand()) . '@test.stormpath.com');
        $account1->setPassword(md5(rand()) . strtoupper(md5(rand())));
        $account1->setGivenName('Test');
        $account1->setMiddleName('User');
        $account1->setSurname('One');
        $account1->setDirectory($this->directory);

        $account2 = new Account;
        $account2->setUsername(md5(rand()));
        $account2->setEmail(md5(rand()) . '@test.stormpath.com');
        $account2->setPassword(md5(rand()) . strtoupper(md5(rand())));
        $account2->setGivenName('Test');
        $account2->setMiddleName('User');
        $account2->setSurname('Two');
        $account2->setDirectory($this->directory);

        $account3 = new Account;
        $account3->setUsername(md5(rand()));
        $account3->setEmail(md5(rand()) . '@test.stormpath.com');
        $account3->setPassword(md5(rand()) . strtoupper(md5(rand())));
        $account3->setGivenName('Test');
        $account3->setMiddleName('User');
        $account3->setSurname('Three');
        $account3->setDirectory($this->directory);

        $resourceManager->persist($account1);
        $resourceManager->persist($account2);
        $resourceManager->persist($account3);
        $resourceManager->flush();

        $this->assertEquals(3, sizeof($this->directory->getAccounts()));
    }
}
