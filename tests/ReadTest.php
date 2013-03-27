<?php

class ReadTest extends PHPUnit_Framework_TestCase {

    private $client;

    public function setUp() {

        $builder = new Services_Stormpath_Client_ClientBuilder;
        $this->client = $builder->setApiKeyFileLocation($_SERVER['HOME'].'/.stormpath/apiKey.yml')->setBaseURL('http://localhost:8080/v1')->build();

    }

    public function testGetTenant() {

        $className = 'Services_Stormpath_Resource_Tenant';
        $tenant = $this->client->getCurrentTenant();

        $this->assertInstanceOf($className, $tenant);
        $this->assertInternalType('string', $tenant->getName());
        $this->assertInternalType('string', $tenant->getKey());

        $className = 'Services_Stormpath_Resource_ApplicationList';
        $applicationList = $tenant->getApplications();

        $this->assertInstanceOf($className, $applicationList);

        // checking the tenant's' applications
        foreach($applicationList as $app)
        {
            // just checking that at least one
            // application property can be read from here
            $this->assertInternalType('string', $app->getName());
        }

        $className = 'Services_Stormpath_Resource_DirectoryList';
        $directoryList = $tenant->getDirectories();

        $this->assertInstanceOf($className, $directoryList);

        // checking the tenant's' directories
        foreach($directoryList as $dir)
        {
            // just checking that at least one
            // directory property can be read from here
            $this->assertInternalType('string', $dir->getName());
        }
    }

    public function testGetApplication() {

        $className = 'Services_Stormpath_Resource_Application';
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
        $this->assertInternalType('string', $application->getDescription());
        $this->assertTrue(array_key_exists($application->getStatus(), Services_Stormpath::$Statuses));

        $className = 'Services_Stormpath_Resource_Tenant';
        $this->assertInstanceOf($className, $application->getTenant());

        $className = 'Services_Stormpath_Resource_AccountList';
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

        $className = 'Services_Stormpath_Resource_Directory';
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
        $this->assertInternalType('string', $directory->getDescription());
        $this->assertTrue(array_key_exists($directory->getStatus(), Services_Stormpath::$Statuses));

        $className = 'Services_Stormpath_Resource_Tenant';
        $this->assertInstanceOf($className, $directory->getTenant());

        $className = 'Services_Stormpath_Resource_AccountList';
        $accountList = $directory->getAccounts();
        $this->assertInstanceOf($className, $accountList);

        foreach($accountList as $acc)
        {
            // just checking that at least one
            // account property can be read from here
            $this->assertInternalType('string', $acc->getUsername());
        }

        $className = 'Services_Stormpath_Resource_GroupList';
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

        $className = 'Services_Stormpath_Resource_Group';
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
        $this->assertInternalType('string', $group->getDescription());
        $this->assertTrue(array_key_exists($group->getStatus(), Services_Stormpath::$Statuses));

        $className = 'Services_Stormpath_Resource_Tenant';
        $this->assertInstanceOf($className, $group->getTenant());

        $className = 'Services_Stormpath_Resource_Directory';
        $this->assertInstanceOf($className, $group->getDirectory());

        $className = 'Services_Stormpath_Resource_AccountList';
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

        $className = 'Services_Stormpath_Resource_Account';
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
        $this->assertTrue(array_key_exists($account->getStatus(), Services_Stormpath::$Statuses));

        $className = 'Services_Stormpath_Resource_Directory';
        $this->assertInstanceOf($className, $account->getDirectory());

        //email verification token is not always available so
        //this test is optional
        /*$className = 'Services_Stormpath_Resource_EmailVerificationToken';
        $this->assertInstanceOf($className, $account->getEmailVerificationToken());*/

        $className = 'Services_Stormpath_Resource_GroupList';
        $groupList = $account->getGroups();
        $this->assertInstanceOf($className, $groupList);

        foreach($groupList as $group)
        {
            // just checking that at least one
            // group property can be read from here
            $this->assertInternalType('string', $group->getName());
        }

        $className = 'Services_Stormpath_Resource_GroupMembershipList';
        $groupMembershipList = $account->getGroupMemberShips();
        $this->assertInstanceOf($className, $groupMembershipList);

        foreach($groupMembershipList as $groupMembership)
        {
            $className = 'Services_Stormpath_Resource_Account';
            $this->assertInstanceOf($className, $groupMembership->getAccount());

            $className = 'Services_Stormpath_Resource_Group';
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

        $properties = new stdClass();
        $properties->href = $href;

        $directory = $this->client->getDataStore()->instantiate(Services_Stormpath::DIRECTORY, $properties);

        $name = 'Name Before Materialization';

        $directory->setName($name);

        $this->assertInternalType('string', $directory->getDescription());

        $this->assertTrue($directory->getName() == $name);
    }

}