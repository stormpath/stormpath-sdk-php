<?php


class WriteTest extends PHPUnit_Framework_TestCase
{

    private $addAccountToGroup;
    private $addGroupToAccount;
    private $changePassword;
    private $client;
    private $createAccount;
    private $createAccountWorkflowOverride;
    private $createAccountWithGroup;
    private $createApplication;
    private $sendPasswordResetEmail;
    private $modifyAccount;
    private $modifyDeleteAccount;
    private $modifyApplication;
    private $modifyDirectory;
    private $modifyGroup;
    private $modifyGroupMembership;
    private $verifyEmailVerificationToken;
    private $verifyPasswordResetToken;

    public function setUp() {

        $builder = new Services_Stormpath_Client_ClientBuilder;
        $this->client = $builder->setApiKeyFileLocation($_SERVER['HOME'].'/.stormpath/apiKey.yml')->setBaseURL('http://localhost:8080/v1')->build();

    }

    public function testSuccessfulAuthentication()
    {
        $href = 'applications/uGBNDZ7TRhm_tahanqvn9A';
        $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

        $result = $application->authenticateAccount(new Services_Stormpath_Authc_UsernamePasswordRequest('kentucky', 'super_P4ss'));

        $className = 'Services_Stormpath_Authc_AuthenticationResult';
        $this->assertInstanceOf($className, $result);

        $className = 'Services_Stormpath_Resource_Account';
        $this->assertInstanceOf($className, $result->getAccount());
    }

    /**
     * @expectedException Services_Stormpath_Resource_ResourceError
     */
    public function testFailedAuthentication()
    {
        $href = '/applications/uGBNDZ7TRhm_tahanqvn9A';
        $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

        $application->authenticateAccount(new Services_Stormpath_Authc_UsernamePasswordRequest('kentucky', 'badPass'));

    }

    public function testCreateAccount()
    {
        if ($this->createAccount)
        {
            $href = 'directories/18k4m10DdrE3J4iEXw4Sq5';
            $directory = $this->client->getDataStore()->getResource($href, Services_Stormpath::DIRECTORY);

            $email = 'phpsdk2@email.com';
            $account = $this->client->getDataStore()->instantiate(Services_Stormpath::ACCOUNT);
            $account->setEmail($email);
            $account->setGivenName('Php');
            $account->setPassword('super_P4ss');
            $account->setSurname('Sdk');
            $account->setUsername('phpsdk2');

            try {

                $createdAccount = $directory->createAccount($account);
                $className = 'Services_Stormpath_Resource_Account';
                $this->assertTrue($account->getHref() ? true : false);
                $this->assertInstanceOf($className, $createdAccount);

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
            $href = 'directories/18k4m10DdrE3J4iEXw4Sq5';
            $directory = $this->client->getDataStore()->getResource($href, Services_Stormpath::DIRECTORY);

            $email = 'phpsdkworkflowoverride@email.com';
            $account = $this->client->getDataStore()->instantiate(Services_Stormpath::ACCOUNT);
            $account->setEmail($email);
            $account->setGivenName('Php');
            $account->setPassword('super_P4ss');
            $account->setSurname('Sdk Workflow Override');
            $account->setUsername('phpsdkworkflowoverride');

            try {

                $createdAccount = $directory->createAccount($account, false);
                $className = 'Services_Stormpath_Resource_Account';
                $this->assertTrue($account->getHref() ? true : false);
                $this->assertInstanceOf($className, $createdAccount);

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

            date_default_timezone_set('UTC');
            $modValue = 'Modified at: '. date_format(date_create(), 'Y-m-d H:i:s') . ' by PHPSdk';
            $account->setMiddleName($modValue);

            $account->save();

            $this->assertTrue($account->getMiddleName() == $modValue);
        }
    }

    public function testModifyDeleteAccount()
    {
        if ($this->modifyDeleteAccount)
        {
            $href = '/accounts/fQ33FHd3Rnuk2rAaiCuGqQ';
            $account = $this->client->getDataStore()->getResource($href, Services_Stormpath::ACCOUNT);

            date_default_timezone_set('UTC');
            $modValue = 'Modified at: '. date_format(date_create(), 'Y-m-d H:i:s') . ' by PHPSdk';
            $account->setMiddleName($modValue);

            $account->save();

            $this->assertTrue($account->getMiddleName() == $modValue);

            $account->setMiddleName(null);

            $account->save();

            $this->assertTrue($account->getMiddleName() == null);
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

    public function testChangePassword()
    {
        if ($this->changePassword)
        {
            $href = 'applications/fzyWJ5V_SDORGPk4fT2jhA';
            $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

            $account = $application->verifyPasswordResetToken('TFMWt3lbQdWc7MNF48pJbw');

            $className = 'Services_Stormpath_Resource_Account';
            $this->assertInstanceOf($className, $account);

            $newPassword = 'new_P4ss';
            $account->setPassword($newPassword);
            $account->save();

            $result = true;
            try {

                $application->authenticateAccount(new Services_Stormpath_Authc_UsernamePasswordRequest($account->getUsername(), $newPassword));

            } catch (Services_Stormpath_Resource_ResourceError $re)
            {
                $result = false;
            }

            $this->assertTrue($result);
        }
    }

    public function testAddAccountToGroup()
    {
        if ($this->addAccountToGroup)
        {
            $groupHref = 'groups/0I5wmUILTvqHraXLERE9fw';
            $group = $this->client->getDataStore()->getResource($groupHref, Services_Stormpath::GROUP);

            $accountHref = 'accounts/RpB0hBFVSTmoLZTqHlwBRg';
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
            $groupHref = 'groups/E_D6HFfxSFmP0wIRvvvMUA';
            $group = $this->client->getDataStore()->getResource($groupHref, Services_Stormpath::GROUP);

            $accountHref = 'accounts/RpB0hBFVSTmoLZTqHlwBRg';
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
            $groupHref = 'groups/0I5wmUILTvqHraXLERE9fw';
            $group = $this->client->getDataStore()->getResource($groupHref, Services_Stormpath::GROUP);

            $accountHref = 'accounts/RpB0hBFVSTmoLZTqHlwBRg';
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

            } else
            {
                $groupMembership = $account->addGroup($group);
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
