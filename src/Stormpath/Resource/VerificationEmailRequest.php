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

class VerificationEmailRequest
{
    private $login;
    private $accountStore;

    public function __construct($login, array $options = array())
    {
        $this->login = $login;

        if (isset($options['accountStore']))
        {
            $accountStore = $options['accountStore'];
            if ($accountStore instanceof AccountStore)
            {
                $this->accountStore = $accountStore;
            }
            else
            {
                throw new \InvalidArgumentException("The value for accountStore in the \$options array should be an instance of \\Stormpath\\Resource\\AccountStore");
            }
        }

    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getAccountStore()
    {
        return $this->accountStore;
    }
}