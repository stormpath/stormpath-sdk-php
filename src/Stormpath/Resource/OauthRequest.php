<?php


namespace Stormpath\Resource;


class OauthRequest extends Resource
{

    const USERNAME      = 'username';
    const PASSWORD      = 'password';
    const GRANT_TYPE    = 'grant_type';

    public function getUsername()
    {
        return $this->getProperty(self::USERNAME);
    }

    public function setUsername($username)
    {
        $this->setProperty(self::USERNAME, $username);
        return $this;
    }

    public function getPassword()
    {
        return $this->getProperty(self::PASSWORD);
    }

    public function setPassword($password)
    {
        $this->setProperty(self::PASSWORD, $password);
        return $this;
    }

    public function getGrantType()
    {
        return $this->getProperty(self::GRANT_TYPE);
    }

    public function setGrantType($grantType)
    {
        $this->setProperty(self::GRANT_TYPE, $grantType);
        return $this;
    }
}