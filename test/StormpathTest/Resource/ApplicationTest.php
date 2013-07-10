<?php

namespace StormpathTest\Resource;

use PHPUnit_Framework_TestCase;
use Stormpath\Service\StormpathService;
use Stormpath\Resource\Application;

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
}
