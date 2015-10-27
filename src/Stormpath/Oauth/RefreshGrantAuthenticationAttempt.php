<?php


namespace Stormpath\Oauth;

use Stormpath\Resource\Resource;

class RefreshGrantAuthenticationAttempt extends Resource
{
    const REFRESH_TOKEN     = 'refresh_token';
    const GRANT_TYPE        = 'grant_type';

    public function setRefreshToken($refreshToken) {
        $this->setProperty(self::REFRESH_TOKEN, $refreshToken);
        return $this;
    }

    public function setGrantType($grantType) {
        $this->setProperty(self::GRANT_TYPE, $grantType);
        return $this;
    }

    public function getRefreshToken() {
        return $this->getProperty(self::REFRESH_TOKEN);
    }

    public function getGrantType() {
        return $this->getProperty(self::REFRESH_TOKEN);
    }
}