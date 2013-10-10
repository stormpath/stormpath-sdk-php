<?php

namespace Stormpath\Resource;

/*
 * Copyright 2013 Stormpath, Inc.
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

use Stormpath\Stormpath;

class Directory extends InstanceResource
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
        if (array_key_exists($status, Stormpath::$Statuses))
        {
            $this->setProperty(self::STATUS, Stormpath::$Statuses[$status]);
        }
    }

    public function createAccount(Account $account, $registrationWorkflowEnabled = true)
    {
        $accounts = $this->getAccounts();
        $href = $accounts->getHref();

        if (!$registrationWorkflowEnabled)
        {
            $href .= '?registrationWorkflowEnabled=' . var_export($registrationWorkflowEnabled, true);
        }

        return $this->getDataStore()->create($href, $account, Stormpath::ACCOUNT);
    }

    public function getAccounts()
    {
        return $this->getResourceProperty(self::ACCOUNTS, Stormpath::ACCOUNT_LIST);
    }

    public function getGroups()
    {
        return $this->getResourceProperty(self::GROUPS, Stormpath::GROUP_LIST);
    }

    public function getTenant()
    {
        return $this->getResourceProperty(self::TENANT, Stormpath::TENANT);
    }
}
