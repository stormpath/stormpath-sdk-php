<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class Tenant extends InstanceResource
{
    const NAME         = "name";
    const KEY          = "key";
    const APPLICATIONS = "applications";
    const DIRECTORIES  = "directories";

    public function getName()
    {
        return $this->getProperty(self::NAME);
    }

    public function getKey()
    {
        return $this->getProperty(self::KEY);
    }

    public function createApplication(Application $application)
    {
        //TODO: enable auto discovery
        return $this->getDataStore()->create('/applications', $application, StormpathService::APPLICATION);
    }

    public function getApplications()
    {

        return $this->getResourceProperty(self::APPLICATIONS, StormpathService::APPLICATION_LIST);
    }

    public function getDirectories()
    {

        return $this->getResourceProperty(self::DIRECTORIES, StormpathService::DIRECTORY_LIST);
    }

    public function verifyAccountEmail($token)
    {
        //TODO: enable auto discovery via Tenant resource (should be just /emailVerificationTokens)
        $href = "/accounts/emailVerificationTokens/" . $token;

        $tokenProperties = new stdClass();
        $hrefName = self::HREF_PROP_NAME;
        $tokenProperties->$hrefName = $href;

        $evToken = $this->getDataStore()->instantiate(StormpathService::EMAIL_VERIFICATION_TOKEN, $tokenProperties);

        //execute a POST (should clean this up / make it more obvious)
        return $this->getDataStore()->save($evToken, StormpathService::ACCOUNT);
    }
}
