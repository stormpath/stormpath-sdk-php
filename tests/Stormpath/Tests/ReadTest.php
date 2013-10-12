<?php

namespace Stormpath\Tests;

class ReadTest extends \PHPUnit_Framework_TestCase {

    private $client;

    public function setUp() {

        $builder = new \Stormpath\ClientBuilder;
        $this->client = $builder->setApiKeyFileLocation($_SERVER['HOME'].'/.stormpath/apiKey.properties')->setBaseURL('http://localhost:8080/v1')->build();

    }

    public function testGetTenant() {

        $className = '\Stormpath\Resource\\Tenant';
        $tenant = $this->client->getCurrentTenant();

        $this->assertInstanceOf($className, $tenant);
        $this->assertInternalType('string', $tenant->name);
        $this->assertInternalType('string', $tenant->key);

        $className = '\Stormpath\Resource\\ApplicationList';
        $applicationList = $tenant->applications;

        $this->assertInstanceOf($className, $applicationList);

        // checking the tenant's' applications
        foreach($applicationList as $app)
        {
            // just checking that at least one
            // application property can be read from here
            $this->assertInternalType('string', $app->name);
        }

        $className = '\Stormpath\Resource\\DirectoryList';
        $directoryList = $tenant->directories;

        $this->assertInstanceOf($className, $directoryList);

        // checking the tenant's' directories
        foreach($directoryList as $dir)
        {
            // just checking that at least one
            // directory property can be read from here
            $this->assertInternalType('string', $dir->name);
        }
    }

    public function testGetApplication() {

        $className = '\Stormpath\Resource\\Application';
        $tenant = $this->client->getCurrentTenant();

        $application = null;
        foreach($tenant->getApplications() as $app)
        {
            // taking the first application that shows up
            // to read its properties
            $application = $app;
            break;
        }

        $this->assertInstanceOf($className, $application);

        $this->assertInternalType('string', $application->getName());
        $this->assertTrue(array_key_exists($application->getStatus(), \Stormpath\Stormpath::$Statuses));

        $className = '\Stormpath\Resource\\Tenant';
        $this->assertInstanceOf($className, $application->getTenant());

        $className = '\Stormpath\Resource\\AccountList';
        $accountList = $application->getAccounts();
        $this->assertInstanceOf($className, $accountList);

        foreach($accountList as $acc)
        {
            // just checking that at least one
            // account property can be read from here
            $this->assertInternalType('string', $acc->getUsername());
        }

    }

    public function testGetDirectory() {

        $className = '\Stormpath\Resource\\Directory';
        $tenant = $this->client->getCurrentTenant();

        $directory = null;
        foreach($tenant->getDirectories() as $dir)
        {
            // taking the first directory that shows up
            // to read its properties
            $directory = $dir;
            break;
        }

        $this->assertInstanceOf($className, $directory);

        $this->assertInternalType('string', $directory->getName());
        $this->assertTrue(array_key_exists($directory->getStatus(), \Stormpath\Stormpath::$Statuses));

        $className = '\Stormpath\Resource\\Tenant';
        $this->assertInstanceOf($className, $directory->getTenant());

        $className = '\Stormpath\Resource\\AccountList';
        $accountList = $directory->getAccounts();
        $this->assertInstanceOf($className, $accountList);

        foreach($accountList as $acc)
        {
            // just checking that at least one
            // account property can be read from here
            $this->assertInternalType('string', $acc->getUsername());
        }

        $className = '\Stormpath\Resource\\GroupList';
        $groupList = $directory->getGroups();
        $this->assertInstanceOf($className, $groupList);

        foreach($groupList as $group)
        {
            // just checking that at least one
            // group property can be read from here
            $this->assertInternalType('string', $group->getName());
        }
    }

    public function testGetGroup() {

        $className = '\Stormpath\Resource\\Group';
        $tenant = $this->client->getCurrentTenant();

        $group = null;
        foreach($tenant->getDirectories() as $dir)
        {
            $hasGroup = false;
            foreach($dir->getGroups() as $grp)
            {
                // taking the first group that shows up
                // to read its properties
                $group = $grp;
                $hasGroup = true;
            }

            if ($hasGroup)
            {
                break;
            }
        }

        $this->assertInstanceOf($className, $group);

        $this->assertInternalType('string', $group->getName());
        $this->assertTrue(array_key_exists($group->getStatus(), \Stormpath\Stormpath::$Statuses));

        $className = '\Stormpath\Resource\\Tenant';
        $this->assertInstanceOf($className, $group->getTenant());

        $className = '\Stormpath\Resource\\Directory';
        $this->assertInstanceOf($className, $group->getDirectory());

        $className = '\Stormpath\Resource\\AccountList';
        $accountList = $group->getAccounts();
        $this->assertInstanceOf($className, $accountList);

        foreach($accountList as $acc)
        {
            // just checking that at least one
            // account property can be read from here
            $this->assertInternalType('string', $acc->getUsername());
        }
    }

    public function testGetAccount() {

        $className = '\Stormpath\Resource\\Account';
        $tenant = $this->client->getCurrentTenant();

        $account = null;
        foreach($tenant->getDirectories() as $dir)
        {
            $hasAccount = false;
            foreach($dir->getAccounts() as $acc)
            {
                // taking the first group that shows up
                // to read its properties
                $account = $acc;
                $hasAccount = true;
            }

            if ($hasAccount)
            {
                break;
            }
        }

        $this->assertInstanceOf($className, $account);

        $this->assertInternalType('string', $account->getUsername());
        $this->assertInternalType('string', $account->getEmail());
        $this->assertInternalType('string', $account->getGivenName());
        // middle name is optional
        //$this->assertInternalType('string', $account->getMiddleName());
        $this->assertInternalType('string', $account->getSurname());
        $this->assertTrue(array_key_exists($account->getStatus(), \Stormpath\Stormpath::$Statuses));

        $className = '\Stormpath\Resource\\Directory';
        $this->assertInstanceOf($className, $account->getDirectory());

        //email verification token is not always available so
        //this test is optional
        /*$className = '\Stormpath\Resource\\EmailVerificationToken';
        $this->assertInstanceOf($className, $account->getEmailVerificationToken());*/

        $className = '\Stormpath\Resource\\GroupList';
        $groupList = $account->getGroups();
        $this->assertInstanceOf($className, $groupList);

        foreach($groupList as $group)
        {
            // just checking that at least one
            // group property can be read from here
            $this->assertInternalType('string', $group->getName());
        }

        $className = '\Stormpath\Resource\\GroupMembershipList';
        $groupMembershipList = $account->getGroupMemberShips();
        $this->assertInstanceOf($className, $groupMembershipList);

        foreach($groupMembershipList as $groupMembership)
        {
            $className = '\Stormpath\Resource\\Account';
            $this->assertInstanceOf($className, $groupMembership->getAccount());

            $className = '\Stormpath\Resource\\Group';
            $this->assertInstanceOf($className, $groupMembership->getGroup());
        }
    }

    public function testDirtyPropertiesRetainedAfterMaterialization()
    {
        $tenant = $this->client->getCurrentTenant();

        $href = null;
        foreach($tenant->getDirectories() as $dir)
        {
            $href = $dir->getHref();
            break;
        }

        $properties = new \stdClass();
        $properties->href = $href;

        $directory = $this->client->getDataStore()->instantiate(\Stormpath\Stormpath::DIRECTORY, $properties);

        $name = 'Name Before Materialization';

        $directory->setName($name);

        $this->assertInternalType('string', $directory->getDescription());

        $this->assertTrue($directory->getName() == $name);
    }

}