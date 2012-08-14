<?php

class Services_Stormpath_Resource_Tenant
    extends Services_Stormpath_Resource_InstanceResource
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

    public function createApplication(Services_Stormpath_Resource_Application $application)
    {
        //TODO: enable auto discovery
        return $this->getDataStore()->create(self::APPLICATIONS,
                                             $application,
                                             Services_Stormpath::APPLICATION);
    }

    public function getApplications()
    {
        return $this->getResourceProperty(self::APPLICATIONS, Services_Stormpath::APPLICATION_LIST);
    }

    public function getDirectories()
    {
        return $this->getResourceProperty(self::DIRECTORIES, Services_Stormpath::DIRECTORY_LIST);
    }

    public function verifyAccountEmail($token)
    {
        //TODO: enable auto discovery via Tenant resource (should be just /emailVerificationTokens)
        $href = "/accounts/emailVerificationTokens/" . $token;

        $tokenProperties = new stdClass();
        $hrefName = self::HREF_PROP_NAME;
        $tokenProperties->$hrefName = $href;

        $evToken = $this->getDataStore()->instantiate(Services_Stormpath::EMAIL_VERIFICATION_TOKEN, $tokenProperties);

        //execute a POST (should clean this up / make it more obvious)
        return $this->getDataStore()->save($evToken, Services_Stormpath::ACCOUNT);
    }
}
