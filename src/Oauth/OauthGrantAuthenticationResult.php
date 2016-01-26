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