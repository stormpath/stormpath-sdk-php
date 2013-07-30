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
            $name = md5(rand());
            $group->setName($name);
            $group->setDescription('Test Group ' . $i);
            $group->setStatus('ENABLED');
            $group->setDirectory($this->directory);

            $groups[] = $group;
            $resourceManager->persist($group);
        }

        // Two more groups for orderBy testing
        $group = new Group;
        $name = md5(rand());
        $group->setName($name);
        $group->setDescription('First Group When OrderBy = Description ASC');
        $group->setStatus('ENABLED');
        $group->setDirectory($this->directory);

        $groups[] = $group;
        $resourceManager->persist($group);

        // Two more groups for orderBy testing
        $group = new Group;
        $name = md5(rand());
        $group->setName($name);
        $group->setDescription('Z First Group When OrderBy = Description DESC');
        $group->setStatus('ENABLED');
        $group->setDirectory($this->directory);

        $groups[] = $group;
        $resourceManager->persist($group);

        $resourceManager->flush();

        $groupsCollection = $this->directory->getGroups();
        $this->assertEquals(25, sizeof($groupsCollection));

        $groupsCollection->setOffset(25);
        $this->assertEquals(25, sizeof($groupsCollection));

        $groupsCollection->setOffset(50);
        $this->assertEquals(3, sizeof($groupsCollection));

        $groupsCollection->remove(2);
        $this->assertEquals(false, $groupsCollection->offsetExists(2));
        $this->assertEquals(false, $groupsCollection->remove(15));

        $this->assertEquals(0, $groupsCollection->key());
        $groupsCollection->setOffset(50);
        $offset = $groupsCollection->getOffset();

        $groupsCollection->offsetSet($offset, 20);
        $this->assertEquals(20, $groupsCollection->offsetGet($offset));
        $groupsCollection->offsetUnset($offset);
        $this->assertEquals(0, $groupsCollection->offsetGet($offset));

        $groupsCollection->setLimit(100);
        $groupsCollection->setOffset(0);
        $this->assertEquals(53, sizeof($groupsCollection));

        $groupsCollection->setSearch(array(
            'name' => $name
        ));
        $this->assertEquals(1, sizeof($groupsCollection));

        $groupsCollection->setOrderBy(array('description' => 'ASC'));
        $groupsCollection->setSearch(null);
        $groupsCollection->setLimit(1);
        $groupsCollection->setOffset(0);

        $group = $groupsCollection->first();
        $this->assertEquals('First Group When OrderBy = Description ASC', $group->getDescription());

        $groupsCollection->setOrderBy(array('description' => 'DESC'));
        $groupsCollection->setSearch(null);
        $groupsCollection->setLimit(1);
        $groupsCollection->setOffset(0);

        $group = $groupsCollection->first();
        $this->assertEquals('Z First Group When OrderBy = Description DESC', $group->getDescription());

        $this->assertEquals(true, is_array($groupsCollection->toArray()));

        $this->assertEquals(false, $groupsCollection->contains($name));

        $this->assertEquals(false, $groupsCollection->isEmpty());
  
        $this->assertEquals(null, $groupsCollection->get(5));

        // Clean Up
        foreach ($groups as $group) {
            $resourceManager->remove($group);
        }

        $resourceManager->flush();
    }
}
