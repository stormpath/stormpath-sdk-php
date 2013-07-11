<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class Group extends Instance
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
    
    public function getTenant()
    {
        return $this->getResourceProperty(self::TENANT, StormpathService::TENANT);
    }
    
    public function getAccounts()
    {
        return $this->getResourceProperty(self::ACCOUNTS, StormpathService::ACCOUNT_LIST);
    }
    
    public function getDirectory()
    {
        return $this->getResourceProperty(self::DIRECTORY, StormpathService::DIRECTORY);
    }
    
    public function addAccount(Account $account)
    {
        return GroupMembership::_create($account, $this, $this->getDataStore());
    }
}