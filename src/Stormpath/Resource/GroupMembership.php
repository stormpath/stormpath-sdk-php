<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;

class GroupMembership extends Resource
{
    const ACCOUNT = "account";
    const GROUP = "group";
    
    public function getAccount()
    {
        return $this->getResourceProperty(self::ACCOUNT, StormpathService::ACCOUNT);
    }
    
    public function getGroup()
    {
        return $this->getResourceProperty(self::GROUP, StormpathService::GROUP);
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
    public static function _create(Account $account, Group $group, InternalDataStore $dataStore)
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
        
        $groupMembership = $dataStore->instantiate(StormpathService::GROUP_MEMBERSHIP, $properties);
        
        return $dataStore->create($href, $groupMembership, StormpathService::GROUP_MEMBERSHIP);
    }
}