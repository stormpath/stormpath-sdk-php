<?php

namespace Stormpath\Authc;

class UsernamePasswordRequest implements AuthenticationRequest
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
