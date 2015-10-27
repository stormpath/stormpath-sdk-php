<?php


namespace Stormpath\Resource;


use Stormpath\Resource\InstanceResource;

class GrantAuthenticationToken extends InstanceResource
{
    const ACCESS_TOKEN          = 'access_token';
    const REFRESH_TOKEN         = 'refresh_token';
    const TOKEN_TYPE            = 'token_type';
    const EXPIRES_IN            = 'expires_in';
    const ACCESS_TOKEN_HREF     = 'stormpath_access_token_href';

    public function getAccessToken()
    {
        return $this->getProperty(self::ACCESS_TOKEN);
    }

    public function getRefreshToken()
    {
        return $this->getProperty(self::REFRESH_TOKEN);
    }

    public function getTokenType()
    {
        return $this->getProperty(self::TOKEN_TYPE);
    }

    public function getExpiresIn()
    {
        return $this->getProperty(self::EXPIRES_IN);
    }

    public function getAccessTokenHref()
    {
        return $this->getProperty(self::ACCESS_TOKEN_HREF);
    }

    public function getAsAccessToken()
    {
        $props = new \stdClass();
        $props->href = $this->getAccessTokenHref();
        return $this->getDataStore()->instantiate('AccessToken', $props);
    }


}