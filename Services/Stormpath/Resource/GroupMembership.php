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

class Services_Stormpath_Resource_GroupMembership
    extends Services_Stormpath_Resource_Resource
{
    const ACCOUNT = "account";
    const GROUP = "group";

    public function getAccount()
    {
        return $this->getResourceProperty(self::ACCOUNT, Services_Stormpath::ACCOUNT);
    }

    public function getGroup()
    {
        return $this->getResourceProperty(self::GROUP, Services_Stormpath::GROUP);
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
     * @param $dataStore the datastore used to create the membership
     * @return the created GroupMembership instance.
     */
    public static function _create(Services_Stormpath_Resource_Account $account, Services_Stormpath_Resource_Group $group,
                                  Services_Stormpath_DataStore_InternalDataStore $dataStore)
    {
        //TODO: enable auto discovery
        $href = "/groupMemberships";
        $hrefPropName = self::HREF_PROP_NAME;

        $accountProps = new stdClass();
        $accountProps->$hrefPropName = $account->getHref();

        $groupProps = new stdClass();
        $groupProps->$hrefPropName = $group->getHref();

        $accountsPropName = self::ACCOUNT;
        $groupsPropName = self::GROUP;
        $properties = new stdClass();
        $properties->$accountsPropName = $accountProps;
        $properties->$groupsPropName = $groupProps;

        $groupMembership = $dataStore->instantiate(Services_Stormpath::GROUP_MEMBERSHIP, $properties);

        return $dataStore->create($href, $groupMembership, Services_Stormpath::GROUP_MEMBERSHIP);
    }

}
