<?php

namespace Stormpath\Authc;

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
class UsernamePasswordRequest implements AuthenticationRequest
{
    private $username;
    private $password;
    private $host;
    private $accountStore;

    public function __construct($username, $password, array $options = array())
    {
        $this->password = $password ? str_split($password) : array();
        $this->username = $username;

        $this->host = isset($options['host']) ? $options['host'] : null;
        $this->accountStore = isset($options['accountStore']) ? $options['accountStore'] : null;
    }

    public function getPrincipals()
    {
        return $this->username;
    }

    public function getCredentials()
    {
        return $this->password;
    }

    public function getAccountStore()
    {
        return $this->accountStore;
    }

    // @codeCoverageIgnoreStart
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Clears out (nulls) the username, password, and options.  The password bytes are explicitly set to
     * <tt>0x00</tt> to eliminate the possibility of memory access at a later time.
     */
    public function clear()
    {
        $this->host = null;
        $this->username = null;

        $password = $this->password;

        $this->password = null;

        if ($password)
        {
            foreach($password as $char)
            {
                $char = 0x00;
            }
        }
    }
    // @codeCoverageIgnoreEnd

}
