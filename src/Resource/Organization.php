<?php

/*
 * Copyright 2016 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Stormpath\Resource;

use Stormpath\Client;
use Stormpath\Stormpath;

class Organization extends AccountStore implements Deletable
{
    const ACCOUNTS                          = 'accounts';
    const ACCOUNT_STORE_MAPPINGS            = 'accountStoreMappings';
    const CREATED_AT                        = 'createdAt';
    const CUSTOM_DATA                       = 'customData';
    const DEFAULT_ACCOUNT_STORE_MAPPING     = 'defaultAccountStoreMapping';
    const DEFAULT_GROUP_STORE_MAPPING       = 'defaultGroupStoreMapping';
    const DESCRIPTION                       = 'description';
    const GROUPS                            = 'groups';
    const HREF                              = 'href';
    const MODIFIED_AT                       = 'modifiedAt';
    const NAME                              = 'name';
    const NAME_KEY                          = 'nameKey';
    const STATUS                            = 'status';
    const TENANT                            = 'tenant';

    const PATH                              = 'organizations';

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::ORGANIZATION, self::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::ORGANIZATION, $properties);
    }

    public static function create($properties, array $options = array())
    {
        $organization = $properties;

        if (!($organization instanceof Organization))
        {
            $organization = self::instantiate($properties);
        }

        return Client::create('/'.self::PATH, $organization, $options);
    }

    public function getAccounts(array $options = [])
    {
        return $this->getResourceProperty(self::ACCOUNTS, Stormpath::ACCOUNT, $options);
    }

    public function getAccountStoreMappings(array $options = [])
    {
        return $this->getResourceProperty(self::ACCOUNT_STORE_MAPPINGS, Stormpath::ACCOUNT_STORE_MAPPING_LIST, $options);
    }

    public function getCreatedAt()
    {
        return $this->getProperty(self::CREATED_AT);
    }

    public function getCustomData(array $options = [])
    {
        return $this->getResourceProperty(self::CUSTOM_DATA, Stormpath::CUSTOM_DATA, $options);
    }

    public function getDefaultAccountStoreMapping(array $options = [])
    {
    	return $this->getResourceProperty(self::DEFAULT_ACCOUNT_STORE_MAPPING, Stormpath::ACCOUNT_STORE, $options);
    }

    public function getDefaultGroupStoreMapping(array $options = [])
    {
    	return $this->getResourceProperty(self::DEFAULT_GROUP_STORE_MAPPING, Stormpath::GROUP, $options);
    }

    public function getDescription()
    {
    	return $this->getProperty(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        $this->setProperty(self::DESCRIPTION, $description);
    }

    public function getGroups(array $options = [])
    {
    	return $this->getResourceProperty(self::GROUPS, Stormpath::GROUP_LIST, $options);
    }

    public function getHref()
    {
    	return $this->getProperty(self::HREF);
    }
    
    public function getModifiedAt()
    {
    	return $this->getProperty(self::MODIFIED_AT);
    }
    
    public function getName()
    {
    	return $this->getProperty(self::NAME);
    }

    public function setName($name)
    {
        $this->setProperty(self::NAME, $name);
    }
    
    public function getNameKey()
    {
    	return $this->getProperty(self::NAME_KEY);
    }

    public function setNameKey($nameKey)
    {
        $this->setProperty(self::NAME_KEY, $nameKey);
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
        $uprStatus = strtoupper($status);
        if (array_key_exists($uprStatus, Stormpath::$AccountStatuses))
        {
            $this->setProperty(self::STATUS, Stormpath::$AccountStatuses[$uprStatus]);
        }
    }
    
    public function getTenant(array $options = [])
    {
    	return $this->getResourceProperty(self::TENANT, Stormpath::TENANT, $options);
    }

    public function createOrganizationAccountStoreMapping(AccountStoreMapping $accountStoreMapping, array $options = array())
    {
        return $this->getDataStore()->create("/organizationAccountStoreMappings", $accountStoreMapping, Stormpath::ACCOUNT_STORE_MAPPING);
    }

    public function delete()
    {
        $this->getDataStore()->delete($this);
    }
}