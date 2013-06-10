<?php

namespace Stormpath\Auth;

class UsernamePasswordRequest implements AuthenticationRequest
{
    private $username;
    private $password;
    private $host;

    public function __construct($username,$password,$host)
    {
        $this->username = $username;
        $this->password = $password ? str_split($password) : array();
        $this->host = host;
    }

    public function getPrincipals()
    {
        return $this->username;
    }

    public function getCredentials()
    {
        return $this->password;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function clear()
    {
        $this->username = null;
        $password = $this->password;
        $this->password = null;
        $this-> host = null;

        if($password)
        {
            foreach ($password as $char)
            {
                $char = 0x00;
            }
        }

    }
}

?>
