<?php


class Services_Stormpath_Authc_AuthenticationResult
    extends Services_Stormpath_Resource_Resource
{
    const ACCOUNT = "account";

    public function getAccount()
    {
        return $this->getResourceProperty(self::ACCOUNT, Services_Stormpath::ACCOUNT);
    }
}
