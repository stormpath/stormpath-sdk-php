<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class Account extends InstanceResource
{
    const USERNAME                 = "username";
    const EMAIL                    = "email";
    const PASSWORD                 = "password";
    const GIVEN_NAME               = "givenName";
    const MIDDLE_NAME              = "middleName";
    const SURNAME                  = "surname";
    const STATUS                   = "status";
    const GROUPS                   = "groups";
    const DIRECTORY                = "directory";
    const EMAIL_VERIFICATION_TOKEN = "emailVerificationToken";
    const GROUP_MEMBERSHIPS        = "groupMemberships";

    public function getUsername()
    {

        return $this->getProperty(self::USERNAME);
    }

    public function setUsername($username)
    {
        $this->setProperty(self::USERNAME, $username);
    }

    public function getEmail()
    {

        return $this->getProperty(self::EMAIL);
    }

    public function setEmail($email)
    {
        $this->setProperty(self::EMAIL, $email);
    }

    public function setPassword($password)
    {
        $this->setProperty(self::PASSWORD, $password);
    }

    public function getGivenName()
    {

        return $this->getProperty(self::GIVEN_NAME);
    }

    public function setGivenName($givenName)
    {
        $this->setProperty(self::GIVEN_NAME, $givenName);
    }

    public function getMiddleName()
    {

        return $this->getProperty(self::MIDDLE_NAME);
    }

    public function setMiddleName($middleName)
    {

        return $this->setProperty(self::MIDDLE_NAME, $middleName);
    }

    public function getSurname()
    {

        return $this->getProperty(self::SURNAME);
    }

    public function setSurname($surname)
    {
        $this->setProperty(self::SURNAME, $surname);
    }

    public function getStatus()
    {
        $value = $this->getProperty(self::STATUS);

        if ($value)
        {
            $value = strtoupper($value);
        }

        return $value;
    }

    public function setStatus($status)
    {
        if (array_key_exists($status, StormpathService::$Statuses))
        {
            $this->setProperty(self::STATUS, StormpathService::$Statuses[$status]);
        }
    }

    public function getGroups()
    {

        return $this->getResourceProperty(self::GROUPS, StormpathService::GROUP_LIST);
    }

    public function getDirectory()
    {

        return $this->getResourceProperty(self::DIRECTORY, StormpathService::DIRECTORY);
    }

    public function getEmailVerificationToken()
    {

        return $this->getResourceProperty(self::EMAIL_VERIFICATION_TOKEN, StormpathService::EMAIL_VERIFICATION_TOKEN);
    }

    public function getGroupMemberShips()
    {

        return $this->getResourceProperty(self::GROUP_MEMBERSHIPS, StormpathService::GROUP_MEMBERSHIP_LIST);
    }

    public function addGroup(Group $group)
    {

        return GroupMembership::_create($this, $group, $this->getDataStore());
    }
}
