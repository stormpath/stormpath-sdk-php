<?php
/*
 * Copyright 2016 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


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
        $this->refreshToken = $this->grantAuthenticationToken->getAsRefreshToken();

        return new OauthGrantAuthenticationResult($this);
    }
}