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
