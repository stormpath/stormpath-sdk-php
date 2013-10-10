<?php

namespace Stormpath\Resource;

/*
 * Copyright 2013 Stormpath, Inc.
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

use Stormpath\Stormpath;

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
        return $this->getDataStore()->create('/applications',
                                             $application,
                                             Stormpath::APPLICATION);
    }

    public function getApplications()
    {
        return $this->getResourceProperty(self::APPLICATIONS, Stormpath::APPLICATION_LIST);
    }

    public function getDirectories()
    {
        return $this->getResourceProperty(self::DIRECTORIES, Stormpath::DIRECTORY_LIST);
    }

    public function verifyAccountEmail($token)
    {
        //TODO: enable auto discovery via Tenant resource (should be just /emailVerificationTokens)
        $href = "/accounts/emailVerificationTokens/" . $token;

        $tokenProperties = new stdClass();
        $hrefName = self::HREF_PROP_NAME;
        $tokenProperties->$hrefName = $href;

        $evToken = $this->getDataStore()->instantiate(Stormpath::EMAIL_VERIFICATION_TOKEN, $tokenProperties);

        //execute a POST (should clean this up / make it more obvious)
        return $this->getDataStore()->save($evToken, Stormpath::ACCOUNT);
    }
}
