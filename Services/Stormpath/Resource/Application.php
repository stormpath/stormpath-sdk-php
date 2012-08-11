<?php

class Services_Stormpath_Resource_Application
    extends Services_Stormpath_Resource_InstanceResource
{
    const NAME                  = "name";
    const DESCRIPTION           = "description";
    const STATUS                = "status";
    const TENANT                = "tenant";
    const ACCOUNTS              = "accounts";
    const PASSWORD_RESET_TOKENS = "passwordResetTokens";

    public function getName()
    {
        return $this->getProperty(self::NAME);
    }

    public function setName($name)
    {
        $this->setProperty(self::NAME, $name);
    }

    public function getDescription()
    {
        return $this->getProperty(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        $this->setProperty(self::DESCRIPTION, $description);
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
        if (array_key_exists($status, Services_Stormpath::$Statuses))
        {
            $this->setProperty(self::STATUS, Services_Stormpath::$Statuses[$status]);
        }
    }

    public function getTenant()
    {
        return $this->getResourceProperty(self::TENANT, Services_Stormpath::TENANT);
    }

    public function getAccounts()
    {
        return $this->getResourceProperty(self::ACCOUNTS, Services_Stormpath::ACCOUNT_LIST);
    }

    public function getPasswordResetToken()
    {
        return $this->getResourceProperty(self::PASSWORD_RESET_TOKENS, Services_Stormpath::PASSWORD_RESET_TOKEN);
    }

    public function createPasswordResetToken($email)
    {
        $href = $this->getPasswordResetToken()->getHref();

        $passwordResetProps = new stdClass();

        $passwordResetProps->email = $email;

        $passwordResetToken = $this->getDataStore()->instantiate(Services_Stormpath::PASSWORD_RESET_TOKEN, $passwordResetProps);

        return $this->getDataStore()->create($href, $passwordResetToken, Services_Stormpath::PASSWORD_RESET_TOKEN);
    }

    public function verifyPasswordResetToken($token)
    {
        $href = $this->getPasswordResetToken()->getHref();
        $href .= '/' .$token;

        return $this->getDataStore()->getResource($href, Services_Stormpath::PASSWORD_RESET_TOKEN);
    }

    public function authenticate($request)
    {
        //TODO:implement
    }
}
