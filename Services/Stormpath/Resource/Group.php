<?php


class Services_Stormpath_Resource_Group
    extends Services_Stormpath_Resource_InstanceResource
{
    const NAME = "name";
    const DESCRIPTION = "description";
    const STATUS = "status";
    const TENANT = "tenant";
    const DIRECTORY = "directory";
    const ACCOUNTS = "accounts";

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

    public function getDirectory()
    {
        return $this->getResourceProperty(self::DIRECTORY, Services_Stormpath::DIRECTORY);
    }

    public function addAccount(Services_Stormpath_Resource_Account $account)
    {
        $groupMembership = $this->getDataStore()->instantiate(Services_Stormpath::GROUP_MEMBERSHIP);
        return $groupMembership->create($account, $this);
    }
}
