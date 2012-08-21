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
class Services_Stormpath_Authc_UsernamePasswordRequest
    implements Services_Stormpath_Authc_AuthenticationRequest
{
    private $username;
    private $password;
    private $host;

    public function __construct($username, $password, $host = null)
    {
        $this->host = $host;
        $this->password = $password ? str_split($password) : array();
        $this->username = $username;
    }

    function getPrincipals()
    {
        return $this->username;
    }

    function getCredentials()
    {
        return $this->password;
    }

    function getHost()
    {
        return $this->host;
    }

    /**
     * Clears out (nulls) the username, password, and host.  The password bytes are explicitly set to
     * <tt>0x00</tt> to eliminate the possibility of memory access at a later time.
     */
    function clear()
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

}
