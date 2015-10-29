<?php


namespace Stormpath\Oauth;


class OauthGrantAuthenticationResult
{
    private $accessToken;
    private $accessTokenString;
    private $refreshToken;
    private $refreshTokenString;
    private $accessTokenHref;
    private $tokenType;
    private $expiresIn;

    public function __construct(OauthGrantAuthenticationResultBuilder $builder)
    {
        $this->accessToken          = $builder->getAccessToken();
        $this->accessTokenString    = $builder->getAccessTokenString();
        $this->refreshToken         = $builder->getRefreshToken();
        $this->refreshTokenString   = $builder->getRefreshTokenString();
        $this->accessTokenHref      = $builder->getAccessTokenHref();
        $this->tokenType            = $builder->getTokenType();
        $this->expiresIn            = $builder->getExpiresIn();
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getRefreshTokenString() {
        return $this->refreshTokenString;
    }

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function getAccessTokenHref() {
        return $this->accessTokenHref;
    }

    public function getTokenType() {
        return $this->tokenType;
    }

    public function getExpiresIn() {
        return $this->expiresIn;
    }

    public function getAccessTokenString() {
        return $this->accessTokenString;
    }
}