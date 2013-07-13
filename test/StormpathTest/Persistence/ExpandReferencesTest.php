<?php

namespace StormpathTest\Service;

use StormpathTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Stormpath\Service\StormpathService;
use Stormpath\Http\Client\Adapter\Digest;
use Stormpath\Http\Client\Adapter\Basic;
use Zend\Http\Client;
use Stormpath\Resource\Directory;

class ExpandReferencesTest extends \PHPUnit_Framework_TestCase
{
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

    /**
     * Fetch the current directory with expanding (multiple) references
     */
    public function testFetchDirectoryWithExpandResources()
    {
        $resourceManager = StormpathService::getResourceManager();

        $directory = $resourceManager->find('Stormpath\Resource\Directory', $this->directory->getId(), true);

        $href = $directory->getTenant()->getHref();
        # print_r($directory->getTenant()->getArrayCopy());
    }
}
