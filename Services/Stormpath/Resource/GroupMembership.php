<?php


class Services_Stormpath_Resource_GroupMembership
    extends Services_Stormpath_Resource_InstanceResource
{
    const ACCOUNT = "account";
    const GROUP = "group";

    public function getAccount()
    {
        return $this->getProperty(self::ACCOUNT);
    }

    public function getGroup()
    {
        return $this->getProperty(self::GROUP);
    }

    public function create(Services_Stormpath_Resource_Account $account, Services_Stormpath_Resource_Group $group)
    {
        //TODO: enable auto discovery
        $href = "/groupMemberships";

        $accountProps = [self::HREF_PROP_NAME => $account->getHref()];
        $groupProps = [self::HREF_PROP_NAME => $group->getHref()];

        $properties = [self::ACCOUNT => $accountProps, self::GROUP => $groupProps];

        $groupMembership = $this->getDataStore()->instantiate(Services_Stormpath::GROUP_MEMBERSHIP, $properties);

        return $this->getDataStore()->create($href, $groupMembership, Services_Stormpath::GROUP_MEMBERSHIP);
    }

    public function delete()
    {
        $this->getDataStore()->delete($this);
    }
}
