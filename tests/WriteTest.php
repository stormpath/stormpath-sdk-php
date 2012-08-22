<?php


class WriteTest extends PHPUnit_Framework_TestCase
{

    private $addAccountToGroup;
    private $addGroupToAccount;
    private $client;
    private $createAccount;
    private $createAccountWorkflowOverride;
    private $createAccountWithGroup;
    private $createApplication;
    private $sendPasswordResetEmail;
    private $modifyAccount;
    private $modifyApplication;
    private $modifyDirectory;
    private $modifyGroup;
    private $modifyGroupMembership;
    private $verifyEmailVerificationToken;
    private $verifyPasswordResetToken;

    public function setUp() {

        $this->client = Services_Stormpath::createClient('id', 'secret');

    }

    public function testSuccessfulAuthentication()
    {
        $href = 'applications/fzyWJ5V_SDORGPk4fT2jhA';
        $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

        $result = $application->authenticateAccount(new Services_Stormpath_Authc_UsernamePasswordRequest('kentucky', 'super_P4ss'));

        $className = 'Services_Stormpath_Authc_AuthenticationResult';
        $this->assertInstanceOf($className, $result);

        $className = 'Services_Stormpath_Resource_Account';
        $this->assertInstanceOf($className, $result->getAccount());
    }

    public function testFailedAuthentication()
    {
        $href = '/applications/fzyWJ5V_SDORGPk4fT2jhA';
        $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

        $result = false;
        try {

            $application->authenticateAccount(new Services_Stormpath_Authc_UsernamePasswordRequest('kentucky', 'badPass'));

        } catch (Services_Stormpath_Resource_ResourceError $re)
        {
            $this->assertInternalType('int', $re->getStatus());
            $this->assertInternalType('int', $re->getErrorCode());
            $this->assertInternalType('string', $re->getDeveloperMessage());
            $this->assertInternalType('string', $re->getMoreInfo());
            $this->assertInternalType('string', $re->getMessage());
            $result = true;
        }

        $this->assertTrue($result);
    }

    public function testCreateAccount()
    {
        if ($this->createAccount)
        {
            $href = 'directories/wDTY5jppTLS2uZEAcqaL5A';
            $directory = $this->client->getDataStore()->getResource($href, Services_Stormpath::DIRECTORY);

            $email = 'phpsdk@email.com';
            $account = $this->client->getDataStore()->instantiate(Services_Stormpath::ACCOUNT);
            $account->setEmail($email);
            $account->setGivenName('Php');
            $account->setPassword('super_P4ss');
            $account->setSurname('Sdk');
            $account->setUsername('phpsdk');

            try {

                 $directory->createAccount($account);

            } catch (Services_Stormpath_Resource_ResourceError $re)
            {
                $this->assertTrue(false);
            }

            $accountCreated = false;
            foreach($directory->getAccounts() as $acc)
            {
                if ($acc->getEmail() == $email)
                {
                    $accountCreated = true;
                    break;
                }
            }

            $this->assertTrue($accountCreated);

        }
    }

    public function testCreateAccountWorkflowOverride()
    {
        if ($this->createAccountWorkflowOverride)
        {
            $href = 'directories/wDTY5jppTLS2uZEAcqaL5A';
            $directory = $this->client->getDataStore()->getResource($href, Services_Stormpath::DIRECTORY);

            $email = 'phpsdkworkflowoverride@email.com';
            $account = $this->client->getDataStore()->instantiate(Services_Stormpath::ACCOUNT);
            $account->setEmail($email);
            $account->setGivenName('Php');
            $account->setPassword('super_P4ss');
            $account->setSurname('Sdk Workflow Override');
            $account->setUsername('phpsdkworkflowoverride');

            try {

                $directory->createAccount($account, false);

            } catch (Services_Stormpath_Resource_ResourceError $re)
            {
                $this->assertTrue(false);
            }

            $accountCreated = false;
            foreach($directory->getAccounts() as $acc)
            {
                if ($acc->getEmail() == $email)
                {
                    $accountCreated = true;
                    break;
                }
            }

            $this->assertTrue($accountCreated);

        }
    }

    public function testModifyAccount()
    {
        if ($this->modifyAccount)
        {
            $href = '/accounts/ije9hUEKTZ29YcGhdG5s2A';
            $account = $this->client->getDataStore()->getResource($href, Services_Stormpath::ACCOUNT);

            date_default_timezone_set('America/Los_Angeles');
            $modValue = 'Modified at: '. date_format(date_create(), 'Y-m-d H:i:s') . ' by PHPSdk';
            $account->setMiddleName($modValue);

            $account->save();

            $this->assertTrue($account->getMiddleName() == $modValue);
        }
    }

    public function testModifyApplication()
    {
        if ($this->modifyApplication)
        {
            $href = 'applications/fzyWJ5V_SDORGPk4fT2jhA';
            $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

            date_default_timezone_set('America/Los_Angeles');
            $modValue = 'Modified at: '. date_format(date_create(), 'Y-m-d H:i:s') . ' by PHPSdk';
            $application->setDescription($modValue);

            $application->save();

            $this->assertTrue($application->getDescription() == $modValue);
        }
    }

    public function testModifyDirectory()
    {
        if ($this->modifyDirectory)
        {
            $href = '/directories/wDTY5jppTLS2uZEAcqaL5A';
            $directory = $this->client->getDataStore()->getResource($href, Services_Stormpath::DIRECTORY);

            date_default_timezone_set('America/Los_Angeles');
            $modValue = 'Modified at: '. date_format(date_create(), 'Y-m-d H:i:s') . ' by PHPSdk';
            $directory->setDescription($modValue);

            $directory->save();

            $this->assertTrue($directory->getDescription() == $modValue);
        }
    }

    public function testModifyGroup()
    {
        if ($this->modifyGroup)
        {
            $href = 'groups/mCidbrAcSF-VpkNfOVvJkQ';
            $group = $this->client->getDataStore()->getResource($href, Services_Stormpath::GROUP);

            date_default_timezone_set('America/Los_Angeles');
            $modValue = 'Modified at: '. date_format(date_create(), 'Y-m-d H:i:s') . ' by PHPSdk';
            $group->setDescription($modValue);

            $group->save();

            $this->assertTrue($group->getDescription() == $modValue);
        }
    }

    public function testCreateApplication()
    {
        if ($this->createApplication)
        {
            $tenant = $this->client->getCurrentTenant();

            $application = $this->client->getDataStore()->instantiate(Services_Stormpath::APPLICATION);
            $application->setName('Test Application Creation by PHPSdk');
            $application->setDescription('Test Application Description by PHPSdk');

            $result = $tenant->createApplication($application);

            $className = 'Services_Stormpath_Resource_Application';
            $this->assertInstanceOf($className, $result);
        }
    }

    public function testEmailVerificationToken()
    {
        if ($this->verifyEmailVerificationToken)
        {
            $token = 'tMvnXThLQ1Gk0oVGnzqH-Q';

            $tenant = $this->client->getCurrentTenant();

            $result = $tenant->verifyAccountEmail($token);

            $className = 'Services_Stormpath_Resource_Account';
            $this->assertInstanceOf($className, $result);
        }
    }

    public function testSendPasswordResetEmail()
    {
        if ($this->sendPasswordResetEmail)
        {
            $href = 'applications/fzyWJ5V_SDORGPk4fT2jhA';
            $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

            $result = $application->sendPasswordResetEmail('phpsdk@email.com');

            $className = 'Services_Stormpath_Resource_Account';
            $this->assertInstanceOf($className, $result);
        }
    }

    public function testVerifyPasswordResetToken()
    {
        if ($this->verifyPasswordResetToken)
        {
            $href = 'applications/fzyWJ5V_SDORGPk4fT2jhA';
            $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

            $result = $application->verifyPasswordResetToken('o-Q96TGKT5GG4nOCZegLiw');

            $className = 'Services_Stormpath_Resource_Account';
            $this->assertInstanceOf($className, $result);
        }
    }

    public function testAddAccountToGroup()
    {
        if ($this->addAccountToGroup)
        {
            $groupHref = 'groups/mCidbrAcSF-VpkNfOVvJkQ';
            $group = $this->client->getDataStore()->getResource($groupHref, Services_Stormpath::GROUP);

            $accountHref = 'accounts/ije9hUEKTZ29YcGhdG5s2A';
            $account = $this->client->getDataStore()->getResource($accountHref, Services_Stormpath::ACCOUNT);

            $group->addAccount($account);

            $accountAdded = false;
            foreach($group->getAccounts() as $acc)
            {
                if (strrpos($acc->getHref(), $accountHref))
                {
                    $accountAdded = true;
                    break;
                }
            }

            $this->assertTrue($accountAdded);
        }
    }

    public function testAddGroupToAccount()
    {
        if ($this->addGroupToAccount)
        {
            $groupHref = 'groups/mCidbrAcSF-VpkNfOVvJkQ';
            $group = $this->client->getDataStore()->getResource($groupHref, Services_Stormpath::GROUP);

            $accountHref = 'accounts/ije9hUEKTZ29YcGhdG5s2A';
            $account = $this->client->getDataStore()->getResource($accountHref, Services_Stormpath::ACCOUNT);

            $account->addGroup($group);

            $groupAdded = false;
            foreach($account->getGroups() as $grp)
            {
                if (strrpos($grp->getHref(), $groupHref))
                {
                    $groupAdded = true;
                    break;
                }
            }

            $this->assertTrue($groupAdded);
        }
    }

    public function testCreateAccountWithGroup()
    {
        if ($this->createAccountWithGroup)
        {
            $directoryHref = 'directories/wDTY5jppTLS2uZEAcqaL5A';
            $directory = $this->client->getDataStore()->getResource($directoryHref, Services_Stormpath::DIRECTORY);

            $groupHref = 'groups/mCidbrAcSF-VpkNfOVvJkQ';
            $group = $this->client->getDataStore()->getResource($groupHref, Services_Stormpath::GROUP);

            $email = 'phpsdkwithgroup@email.com';
            $account = $this->client->getDataStore()->instantiate(Services_Stormpath::ACCOUNT);
            $account->setEmail($email);
            $account->setGivenName('Php');
            $account->setPassword('super_P4ss');
            $account->setSurname('Sdk With Group');
            $account->setUsername('phpsdkwithgroup');

            try {

                $directory->createAccount($account);
                $account->addGroup($group);

            } catch (Services_Stormpath_Resource_ResourceError $re)
            {
                $this->assertTrue(false);
            }

            foreach($directory->getAccounts() as $acc)
            {
                if ($acc->getEmail() == $email)
                {
                    $this->assertTrue(true);
                    break;
                }
            }

            $groupAdded = false;
            foreach($account->getGroups() as $grp)
            {
                if (strrpos($grp->getHref(), $groupHref))
                {
                    $groupAdded = true;
                    break;
                }
            }

            $this->assertTrue($groupAdded);

        }
    }

    public function testModifyGroupMembership()
    {
        if ($this->modifyGroupMembership)
        {
            $groupHref = 'groups/mCidbrAcSF-VpkNfOVvJkQ';
            $group = $this->client->getDataStore()->getResource($groupHref, Services_Stormpath::GROUP);

            $accountHref = 'accounts/ije9hUEKTZ29YcGhdG5s2A';
            $account = $this->client->getDataStore()->getResource($accountHref, Services_Stormpath::ACCOUNT);

            $groupMembership = false;
            foreach($account->getGroupMemberShips() as $tmpGroupMembership)
            {
                $tmpGroup = $tmpGroupMembership->getGroup();

                if ($tmpGroup and strrpos($tmpGroup->getHref(), $groupHref))
                {
                    $groupMembership = $tmpGroupMembership;
                    break;
                }
            }

            if ($groupMembership)
            {
                $groupMembership->delete();
            }

            $group->addAccount($account);

            foreach($account->getGroupMemberShips() as $tmpGroupMembership)
            {
                $tmpGroup = $tmpGroupMembership->getGroup();

                if ($tmpGroup and strrpos($tmpGroup->getHref(), $groupHref))
                {
                    $groupMembership = $tmpGroupMembership;
                    break;
                }
            }

            $className = 'Services_Stormpath_Resource_GroupMembership';
            $this->assertInstanceOf($className, $groupMembership);
        }
    }

}
