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

use Stormpath\Stormpath;

class PasswordResetToken extends Resource
{
    const EMAIL = "email";
    const ACCOUNT = "account";
    const ACCOUNT_STORE = "accountStore";
    const PASSWORD = "password";

    // @codeCoverageIgnoreStart
    public function getEmail()
    {
        return $this->getProperty(self::EMAIL);
    }
    // @codeCoverageIgnoreEnd

    public function setEmail($email)
    {
        $this->setProperty(self::EMAIL, $email);
    }

    public function getAccount(array $options = array())
    {
        return $this->getResourceProperty(self::ACCOUNT, Stormpath::ACCOUNT, $options);
    }

    public function setAccountStore($accountStore)
    {
        $this->setProperty(self::ACCOUNT_STORE, $accountStore);
    }

    public function setPassword($password)
    {
        $this->setProperty(self::PASSWORD, $password);
    }

    public function getPassword()
    {
        $this->getProperty(self::PASSWORD);
    }
}
