<?php

namespace Stormpath\Provider;

/*
 * Copyright 2013 Stormpath, Inc.
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

use Stormpath\Resource\FacebookProviderData;
use Stormpath\Resource\ProviderData;

class FacebookProviderAccountRequest implements ProviderAccountRequest
{
    const ACCESS_TOKEN  = 'accessToken';

    private $options;

    function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Loads a given instance of ProviderData with the properties
     * stored internally in the request
     *
     * @param ProviderData $providerData the instance to load with data
     * @return ProviderData the given instance with properties set
     */
    function getProviderData()
    {
        $providerData = new FacebookProviderData();

        $providerData->providerId = FacebookProviderData::PROVIDER_ID;

        if (isset($this->options[self::ACCESS_TOKEN]))
        {
            $providerData->accessToken = $this->options[self::ACCESS_TOKEN];
        }
        else
        {
            throw new \InvalidArgumentException('accessToken must be set for FacebookProviderAccountRequest');
        }

        return $providerData;
    }
}