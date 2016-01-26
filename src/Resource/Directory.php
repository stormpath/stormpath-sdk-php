<?php

namespace Stormpath\Resource;

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

use Stormpath\Client;
use Stormpath\Stormpath;

class Directory extends AccountStore implements Deletable
{
    const NAME        = "name";
    const DESCRIPTION = "description";
    const STATUS      = "status";
    const ACCOUNTS    = "accounts";
    const GROUPS      = "groups";
    const TENANT      = "tenant";
    const PROVIDER    = "provider";
    const CUSTOM_DATA = "customData";
    const ACCOUNT_CREATION_POLICY = "accountCreationPolicy";

    const PATH        = "directories";

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::DIRECTORY, self::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::DIRECTORY, $properties);
    }

    public static function create($properties, array $options = array())
    {
        $directory = $properties;

        if (!($directory instanceof Directory))
        {
            $directory = self::instantiate($properties);
        }

        return Client::create('/'.self::PATH, $directory, $options);
    }

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
        $uprStatus = strtoupper($status);
        if (array_key_exists($uprStatus, Stormpath::$Statuses))
        {
            $this->setProperty(self::STATUS, Stormpath::$Statuses[$uprStatus]);
        }
    }

    public function createAccount(Account $account, array $options = array())
    {
        $accounts = $this->getAccounts();
        $href = $accounts->getHref();

        return $this->getDataStore()->create($href, $account, Stormpath::ACCOUNT, $options);
    }

    public function createGroup(Group $group, array $options = array())
    {
        $groups = $this->getGroups();
        $href = $groups->getHref();

        return $this->getDataStore()->create($href, $group, Stormpath::GROUP, $options);
    }

    public function getAccounts(array $options = array())
    {
        return $this->getResourceProperty(self::ACCOUNTS, Stormpath::ACCOUNT_LIST, $options);
    }

    public function getGroups(array $options = array())
    {
        return $this->getResourceProperty(self::GROUPS, Stormpath::GROUP_LIST, $options);
    }

    public function getCustomData(array $options = array())
    {
        $customData =  $this->getResourceProperty(self::CUSTOM_DATA, Stormpath::CUSTOM_DATA, $options);

        if(!$customData) {
            $customData = new CustomData();
            $this->setProperty(self::CUSTOM_DATA, $customData);
        }

        return $customData;
    }

    public function getTenant(array $options = array())
    {
        return $this->getResourceProperty(self::TENANT, Stormpath::TENANT, $options);
    }

    public function getAccountCreationPolicy(array $options = array())
    {
        return $this->getResourceProperty(self::ACCOUNT_CREATION_POLICY, Stormpath::ACCOUNT_CREATION_POLICY, $options);
    }

    public function setProvider(Provider $provider)
    {
        $this->setProperty(self::PROVIDER, $provider);
    }

    public function getProvider(array $options = array())
    {
        return $this->getResourceProperty(self::PROVIDER, Stormpath::PROVIDER, $options);
    }

    public function delete() {

        $this->getDataStore()->delete($this);
    }
}
