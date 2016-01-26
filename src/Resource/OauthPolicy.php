<?php

namespace Stormpath\Resource;

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

use Stormpath\Client;
use Stormpath\Stormpath;

/**
 * Class OauthPolicy
 * @package Stormpath\Resource
 * @since 1.11.0.beta
 */
class OauthPolicy extends InstanceResource implements Saveable
{
    const ACCESS_TOKEN_TTL              = "accessTokenTtl";
    const REFRESH_TOKEN_TTL             = "refreshTokenTtl";
    const TOKEN_ENDPOINT                = "tokenEndpoint";

    const PATH                          = "oAuthPolicies";


    /**
     * @param $href
     * @param array $options
     * @return mixed
     */
    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::OAUTH_POLICY, self::PATH, $options);
    }

    /**
     * @return string
     */
    public function getAccessTokenTtl()
    {
        return $this->getProperty(self::ACCESS_TOKEN_TTL);
    }

    /**
     * @param $ttl
     */
    public function setAccessTokenTtl($ttl)
    {
        $this->setProperty(self::ACCESS_TOKEN_TTL, $ttl);
    }

    /**
     * @return string
     */
    public function getRefreshTokenTtl()
    {
        return $this->getProperty(self::REFRESH_TOKEN_TTL);
    }

    /**
     * @param $ttl
     */
    public function setRefreshTokenTtl($ttl)
    {
        $this->setProperty(self::REFRESH_TOKEN_TTL, $ttl);
    }

    /**
     * @return null
     */
    public function getTokenEndpoint()
    {
        return $this->getProperty(self::TOKEN_ENDPOINT);
    }

}