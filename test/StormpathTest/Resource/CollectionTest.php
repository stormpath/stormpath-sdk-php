<?php

namespace StormpathTest\Resource;

use PHPUnit_Framework_TestCase;
use Stormpath\Service\StormpathService;
use Stormpath\Resource\Directory;
use Stormpath\Resource\Account;
use Stormpath\Resource\Group;
use Stormpath\Resource\GroupMembership;
use Stormpath\Resource\LoginAttempt;
use Stormpath\Resource\Application;

class CollectionTest extends \PHPUnit_Framework_TestCase
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

    public function testCollectionPagination()
    {
        $resourceManager = StormpathService::getResourceManager();

        $groups = array();
        for ($i = 0; $i < 51; $i++) {
            $group = new Group;
            $group->setName(md5(rand()));
            $group->setDescription('Test Group ' . $i);
            $group->setStatus('ENABLED');
            $group->setDirectory($this->directory);

            $groups[] = $group;
            $resourceManager->persist($group);
        }

        $resourceManager->flush();

        $groupsCollection = $this->directory->getGroups();
        $this->assertEquals(25, sizeof($groupsCollection));

        $groupsCollection->clear();
        $groupsCollection->setOffset(25);
        $this->assertEquals(25, sizeof($groupsCollection));

        $groupsCollection->clear();
        $groupsCollection->setOffset(50);
        $this->assertEquals(1, sizeof($groupsCollection));

        $groupsCollection->clear();
        $groupsCollection->setLimit(100);
        $groupsCollection->setOffset(0);
        $this->assertEquals(51, sizeof($groupsCollection));

        // Clean Up
        foreach ($groups as $group) {
            $resourceManager->remove($group);
        }

        $resourceManager->flush();
    }
}
