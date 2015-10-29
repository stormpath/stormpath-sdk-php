<?php


namespace Stormpath\Oauth;

use Stormpath\Resource\Resource;

class ExchangeIdSiteTokenAttempt extends Resource
{
    const TOKEN             = 'token';
    const GRANT_TYPE        = 'grant_type';

    public function setToken($token) {
        $this->setProperty(self::TOKEN, $token);
        return $this;
    }

    public function setGrantType($grantType) {
        $this->setProperty(self::GRANT_TYPE, $grantType);
        return $this;
    }

    public function getToken() {
        return $this->getProperty(self::TOKEN);
    }

    public function getGrantType() {
        return $this->getProperty(self::REFRESH_TOKEN);
    }
}