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

class FacebookProviderData extends ProviderData
{
    const PROVIDER_ID   = 'facebook';

    const CODE          = 'code';
    const ACCESS_TOKEN  = 'accessToken';
    const REFRESH_TOKEN = 'refreshToken';

    public function getCode()
    {
        return $this->getProperty(self::CODE);
    }

    public function setCode($code)
    {
        $this->setProperty(self::CODE, $code);
    }

    public function getAccessToken()
    {
        return $this->getProperty(self::ACCESS_TOKEN);
    }

    public function setAccessToken($accessToken)
    {
        $this->setProperty(self::ACCESS_TOKEN, $accessToken);
    }

    public function getRefreshToken()
    {
        return $this->getProperty(self::REFRESH_TOKEN);
    }

    public function setRefreshToken($refreshToken)
    {
        $this->setProperty(self::REFRESH_TOKEN, $refreshToken);
    }

}