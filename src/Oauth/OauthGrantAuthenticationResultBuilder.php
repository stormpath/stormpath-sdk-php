<?php


namespace Stormpath\Oauth;


use Stormpath\Resource\GrantAuthenticationToken;

class OauthGrantAuthenticationResultBuilder
{
    private $accessToken;
    private $accessTokenString;
    private $refreshToken;
    private $refreshTokenString;
    private $accessTokenHref;
    private $tokenType;
    private $expiresIn;
    private $isRefreshGrantAuthRequest = false;
    private $grantAuthenticationToken;

    public function __construct(GrantAuthenticationToken $grantAuthenticationToken)
    {
        $this->grantAuthenticationToken = $grantAuthenticationToken;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getAccessTokenString() {
        return $this->accessTokenString;
    }

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function getRefreshTokenString() {
        return $this->refreshTokenString;
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

    public function setIsRefreshAuthGrantRequest($bool)
    {
        $this->isRefreshGrantAuthRequest = $bool;
        return $this;
    }

    public function build()
    {
        $this->accessToken = $this->grantAuthenticationToken->getAsAccessToken();
        $this->accessTokenString = $this->grantAuthenticationToken->getAccessToken();
        $this->refreshTokenString = $this->grantAuthenticationToken->getRefreshToken();
        $this->accessTokenHref = $this->grantAuthenticationToken->getAccessTokenHref();
        $this->tokenType = $this->grantAuthenticationToken->getTokenType();
        $this->expiresIn = $this->grantAuthenticationToken->getExpiresIn();

        if ($this->isRefreshGrantAuthRequest)
            $this->refreshToken = $this->grantAuthenticationToken->getAsRefreshToken();

        return new OauthGrantAuthenticationResult($this);
    }
}