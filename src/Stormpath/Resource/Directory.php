<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class Directory extends Instance
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
        
        if ($value) {
            $value = strtoupper($value);
        }
        
        return $value;
    }
    
    public function setStatus($status)
    {
        if (array_key_exists($status, StormpathService::$Statuses)) {
            $this->setProperty(self::STATUS, StormpathService::$Statuses[$status]);
        }
    }
    
    public function createAccount(Account $account, $registrationWorkflowEnabled = true)
    {
        $accounts = $this->getAccounts();
        $href = $accounts->getHref();
        
        if (!$registrationWorkflowEnabled) {
            $href .= '?registrationWorkflowEnabled=' . var_export($registrationWorkflowEnabled, true);
        }
        
        return $this->getDataStore()->create($href, $account, StormpathService::ACCOUNT);
    }
    
    public function getAccounts()
    {
        return $this->getResourceProperty(self::ACCOUNTS, StormpathService::ACCOUNT_LIST);
    }
    
    public function getGroups()
    {
        return $this->getResourceProperty(self::GROUPS, StormpathService::GROUP_LIST);
    }
    
    public function getTenant()
    {
        return $this->getResourceProperty(self::TENANT, StormpathService::TENANT);
    }
}