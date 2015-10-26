<?php

namespace Stormpath\Resource;

/*
 * Copyright 2015 Stormpath, Inc.
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

use Stormpath\Stormpath;

class OauthPolicy extends InstanceResource implements Saveable
{
    const ACCESS_TOKEN_TTL              = "accessTokenTtl";
    const REFRESH_TOKEN_TTL             = "refreshTokenTtl";
    const TOKEN_ENDPOINT                = "tokenEndpoint";

    const PATH                          = "oAuthPolicies";


    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::OAUTH_POLICY, self::PATH, $options);
    }

    public function getAccessTokenTtl()
    {
        return $this->getProperty(self::ACCESS_TOKEN_TTL);
    }

    public function setAccessTokenTtl($ttl)
    {
        $this->setProperty(self::ACCESS_TOKEN_TTL, $ttl);
    }

    public function getRefreshTokenTtl()
    {
        return $this->getProperty(self::REFRESH_TOKEN_TTL);
    }

    public function setRefreshTokenTtl($ttl)
    {
        $this->setProperty(self::REFRESH_TOKEN_TTL, $ttl);
    }

    public function getTokenEndpoint()
    {
        return $this->getProperty(self::TOKEN_ENDPOINT);
    }

}