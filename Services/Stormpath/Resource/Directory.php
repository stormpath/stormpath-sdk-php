<?php


class Services_Stormpath_Resource_Directory
    extends Services_Stormpath_Resource_InstanceResource
{
    const NAME        = "name";
    const DESCRIPTION = "description";
    const STATUS      = "status";
    const ACCOUNTS    = "accounts";
    const GROUPS      = "groups";
    const TENANT      = "tenant";

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

    public function createAccount(Services_Stormpath_Resource_Account $account, $registrationWorkflowEnabled = true)
    {
        $accounts = $this->getAccounts();
        $href = $accounts->getHref();

        if (!$registrationWorkflowEnabled)
        {
            $href .= '?registrationWorkflowEnabled=' . var_export($registrationWorkflowEnabled, true);
        }

        $this->getDataStore()->create($href, $account, Services_Stormpath::ACCOUNT);
    }

    public function getAccounts()
    {
        return $this->getResourceProperty(self::ACCOUNTS, Services_Stormpath::ACCOUNT_LIST);
    }

    public function getGroups()
    {
        return $this->getResourceProperty(self::GROUPS, Services_Stormpath::GROUP_LIST);
    }

    public function getTenant()
    {
        return $this->getResourceProperty(self::TENANT, Services_Stormpath::TENANT);
    }
}
