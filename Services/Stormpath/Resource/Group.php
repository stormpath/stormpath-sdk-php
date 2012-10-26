<?php

/*
 * Copyright 2012 Stormpath, Inc.
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
        return Services_Stormpath_Resource_GroupMembership::_create($account, $this, $this->getDataStore());
    }
}
