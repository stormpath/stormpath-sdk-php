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

    public function getAsRefreshToken()
    {
        $props = new \stdClass();
        $props->href = $this->getAccessTokenHref();
        return $this->getDataStore()->instantiate('RefreshToken', $props);
    }


}