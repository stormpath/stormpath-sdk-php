<?php


namespace Stormpath\Oauth;


class RefreshGrantRequest
{
    private $grant_type = "refresh_token";
    private $refresh_token;

    public function __construct($refreshToken)
    {
        $this->refresh_token = $refreshToken;
    }

    public function getRefreshToken() {
        return $this->refresh_token;
    }

    public function getGrantType() {
        return $this->grant_type;
    }
}