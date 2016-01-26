<?php

namespace Stormpath\Resource;

/*
 * Copyright 2016 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Stormpath\Client;
use Stormpath\Stormpath;

class Tenant extends InstanceResource
{
    const NAME          = "name";
    const KEY           = "key";
    const APPLICATIONS  = "applications";
    const CUSTOM_DATA   = "customData";
    const DIRECTORIES   = "directories";
    const ORGANIZATIONS = "organizations";

    public static function get(array $options = array())
    {
        return Client::getInstance()->getTenant($options);
    }

    public function getName()
    {
        return $this->getProperty(self::NAME);
    }

    public function getKey()
    {
        return $this->getProperty(self::KEY);
    }

    public function createApplication(Application $application, array $options = array())
    {
        //TODO: enable auto discovery
        return $this->getDataStore()->create('/'.Application::PATH,
                                             $application,
                                             Stormpath::APPLICATION,
                                             $options);
    }

    public function createDirectory(Directory $directory, array $options = array())
    {
        //TODO: enable auto discovery
        return $this->getDataStore()->create('/'.Directory::PATH,
                                            $directory,
                                            Stormpath::DIRECTORY,
                                            $options);
    }

    public function createOrganization(Organization $organization, array $options = array())
    {
        //TODO: enable auto discovery
        return $this->getDataStore()->create('/'.Organization::PATH,
                                            $organization,
                                            Stormpath::ORGANIZATION,
                                            $options);
    }

    public function getApplications(array $options = array())
    {
        return $this->getResourceProperty(self::APPLICATIONS, Stormpath::APPLICATION_LIST, $options);
    }

    public function getDirectories(array $options = array())
    {
        return $this->getResourceProperty(self::DIRECTORIES, Stormpath::DIRECTORY_LIST, $options);
    }

    public function getCustomData(array $options = array())
    {
        $customData =  $this->getResourceProperty(self::CUSTOM_DATA, Stormpath::CUSTOM_DATA, $options);

        if(!$customData) {
            $customData = new CustomData();
            $this->setProperty(self::CUSTOM_DATA, $customData);
        }

        return $customData;
    }

    public function getOrganizations(array $options = [])
    {
    	return $this->getResourceProperty(self::ORGANIZATIONS, Stormpath::ORGANIZATION_LIST, $options);
    }

    // @codeCoverageIgnoreStart
    public function verifyEmailToken($token)
    {
        //TODO: enable auto discovery via Tenant resource (should be just /emailVerificationTokens)
        $href = "/accounts/emailVerificationTokens/" . $token;

        $tokenProperties = new \stdClass();
        $hrefName = Resource::HREF_PROP_NAME;
        $tokenProperties->$hrefName = $href;

        $evToken = $this->dataStore->instantiate(Stormpath::EMAIL_VERIFICATION_TOKEN, $tokenProperties);

        return $this->dataStore->save($evToken, Stormpath::ACCOUNT);
    }
    // @codeCoverageIgnoreEnd
}
