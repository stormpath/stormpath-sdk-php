<?php

/*
 * Copyright 2012 Stormpath, Inc.
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
class Services_Stormpath_Authc_BasicAuthenticator
{

    private $dataStore;

    public function __construct(Services_Stormpath_DataStore_InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function authenticate($parentHref, Services_Stormpath_Authc_AuthenticationRequest $request)
    {
        if (!$parentHref)
        {
            throw new InvalidArgumentException('$parentHref argument must be specified');
        }

        if (!($request instanceof Services_Stormpath_Authc_UsernamePasswordRequest))
        {
            throw new InvalidArgumentException('Only Services_Stormpath_Authc_UsernamePasswordRequest instances are supported.');
        }

        $username = $request->getPrincipals();
        $username = $username ? $username : '';

        $password = $request->getCredentials();
        $password = implode('', $password);

        $value = $username .':' .$password;

        $value = base64_encode($value);

        $attempt = $this->dataStore->instantiate(Services_Stormpath::BASIC_LOGIN_ATTEMPT);
        $attempt->setType('basic');
        $attempt->setValue($value);

        $href = $parentHref . '/loginAttempts';

        return $this->dataStore->create($href, $attempt, Services_Stormpath::AUTHENTICATION_RESULT);
    }
}
