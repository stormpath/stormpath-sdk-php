<?php

namespace Stormpath\Authc;

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

use Stormpath\DataStore\InternalDataStore;
use Stormpath\Stormpath;

class BasicAuthenticator
{

    private $dataStore;

    public function __construct(InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function authenticate($parentHref, AuthenticationRequest $request, array $options = array())
    {
        if (!$parentHref)
        {
            throw new \InvalidArgumentException('$parentHref argument must be specified');
        }

        if (!($request instanceof UsernamePasswordRequest))
        {
            throw new \InvalidArgumentException('Only UsernamePasswordRequest instances are supported.');
        }

        $username = $request->getPrincipals();
        $username = $username ? $username : '';

        $password = $request->getCredentials();
        $password = implode('', $password);

        $value = $username .':' .$password;

        $value = base64_encode($value);

        $attempt = $this->dataStore->instantiate(Stormpath::BASIC_LOGIN_ATTEMPT);
        $attempt->setValue($value);

        if ($request->getAccountStore() != null) {
            $attempt->setAccountStore($request->getAccountStore());
        }

        $href = $parentHref . '/loginAttempts';

        return $this->dataStore->create($href, $attempt, Stormpath::AUTHENTICATION_RESULT, $options);
    }
}
