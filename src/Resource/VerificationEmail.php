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

class VerificationEmail extends Resource
{
    const LOGIN         = 'login';
    const ACCOUNT_STORE = 'accountStore';

    const PATH          = 'verificationEmails';

    public function getLogin()
    {
        return $this->getProperty(self::LOGIN);
    }

    public function setLogin($login)
    {
        $this->setProperty(self::LOGIN, $login);
    }

    public function getAccountStore($options = array())
    {
        return $this->getResourceProperty(self::ACCOUNT_STORE, Stormpath::ACCOUNT_STORE, $options);
    }

    public function setAccountStore(AccountStore $accountStore)
    {
        $this->setResourceProperty(self::ACCOUNT_STORE, $accountStore);
    }


}