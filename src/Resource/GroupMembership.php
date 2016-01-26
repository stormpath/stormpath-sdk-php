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
use Stormpath\DataStore\InternalDataStore;
use Stormpath\Stormpath;

class GroupMembership extends Resource implements Deletable
{
    const ACCOUNT = "account";
    const GROUP   = "group";

    const PATH    = "groupMemberships";

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::GROUP_MEMBERSHIP, self::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::GROUP_MEMBERSHIP, $properties);
    }

    public static function create($properties, array $options = array())
    {
        $groupMembership = $properties;

        if (!($groupMembership instanceof GroupMembership))
        {
            $groupMembership = self::instantiate($properties);
        }

        return self::_create($groupMembership->getAccount(), $groupMembership->getGroup(), Client::getInstance()->getDataStore(), $options);
    }

    public function getAccount(array $options = array())
    {
        return $this->getResourceProperty(self::ACCOUNT, Stormpath::ACCOUNT, $options);
    }

    public function getGroup(array $options = array())
    {
        return $this->getResourceProperty(self::GROUP, Stormpath::GROUP, $options);
    }

    public function setAccount(Account $account)
    {
        $this->setResourceProperty(self::ACCOUNT, $account);
    }

    public function setGroup(Group $group)
    {
        $this->setResourceProperty(self::GROUP, $group);
    }

    public function delete()
    {
        $this->getDataStore()->delete($this);
    }

    /**
     * THIS IS NOT PART OF THE STORMPATH PUBLIC API.  SDK end-users should not call it - it could be removed or
     * changed at any time.  It has the public modifier only as an implementation technique to be accessible to other
     * resource implementations.
     *
     * @param $account the account to associate with the group.
     * @param $group the group which will contain the account.
     * @param $dataStore the datastore used to create the membership.
     * @param $options the options to pass to the group membership creation.
     * @return the created GroupMembership instance.
     */
    public static function _create(Account $account, Group $group, InternalDataStore $dataStore, array $options = array())
    {
        //TODO: enable auto discovery
        $href = '/' .self::PATH;

        $groupMembership = $dataStore->instantiate(Stormpath::GROUP_MEMBERSHIP);
        $groupMembership->setResourceProperty(self::ACCOUNT, $account);
        $groupMembership->setResourceProperty(self::GROUP, $group);

        return $dataStore->create($href, $groupMembership, Stormpath::GROUP_MEMBERSHIP, $options);
    }

}
