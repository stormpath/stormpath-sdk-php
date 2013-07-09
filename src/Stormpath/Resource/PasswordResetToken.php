<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class PasswordResetToken extends Resource
{
    const EMAIL = "email";
    const ACCOUNT = "account";

    public function getEmail()
    {

        return $this->getProperty(self::EMAIL);
    }

    public function setEmail($email)
    {
        $this->setProperty(self::EMAIL, $email);
    }

    public function getAccount()
    {

        return $this->getResourceProperty(self::ACCOUNT, StormpathService::ACCOUNT);
    }
}
