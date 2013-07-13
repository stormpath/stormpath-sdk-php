<?php

namespace StormpathTest\Service;

use StormpathTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Stormpath\Service\StormpathService;
use Stormpath\Http\Client\Adapter\Digest;
use Stormpath\Http\Client\Adapter\Basic;
use Zend\Http\Client;
use Stormpath\Resource\Application;

class ExpandReferencesTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $resourceManager = StormpathService::getResourceManager();

        $dir = new Application;

        $dir->setName(md5(rand()));
        $dir->setDescription('phpunit test application');
        $dir->setStatus('ENABLED');

        $resourceManager->persist($dir);
        $resourceManager->flush();

        $this->application = $dir;
    }

    protected function tearDown()
    {
        $resourceManager = StormpathService::getResourceManager();
        $resourceManager->remove($this->application);
        $resourceManager->flush();
    }

    /**
     * Fetch the current application with expanding (multiple) references
     */
    public function testFetchapplicationWithExpandResources()
    {
        $resourceManager = StormpathService::getResourceManager();

        $application = $resourceManager->find('Stormpath\Resource\Application', $this->application->getId(), true);

        $href = $application->getTenant()->getHref();
        # print_r($application->getTenant()->getArrayCopy());
    }
}
