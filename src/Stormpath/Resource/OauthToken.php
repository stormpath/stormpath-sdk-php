<?php


namespace Stormpath\Resource;


class OauthToken extends Resource
{
    const ACCESS_TOKEN      = 'access_token';
    const REFRESH_TOKEN     = 'refres_token';
    const TOKEN_TYPE        = 'token_type';
    const EXPIRES_IN        = 'expires_in';
    const TOKEN_HREF        = 'stormpath_access_token_href';

    public function getAcessToken()
    {
        $this->getProperty(self::ACCESS_TOKEN);
    }

    public function getRefreshToken()
    {

    }
}