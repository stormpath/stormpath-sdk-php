<?php

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

namespace Stormpath\Oauth;

use JWT;
use Stormpath\Resource\Application;
use Stormpath\Stormpath;

class VerifyAccessToken
{
    /**
     * @var Application
     */
    private $application;
    /**
     * @var bool
     */
    private $localValidation;

    public function __construct(Application $application, $localValidation = false)
    {
        $this->application = $application;
        $this->localValidation = $localValidation;
    }

    public function validate($jwt)
    {
        if($this->localValidation)
            return JWT::decode($jwt, $this->application->dataStore->getApiKey()->getSecret(), ['HS256']);

        $href = $this->application->getHref() . '/authTokens/' . $jwt;

        return $this->application->dataStore->getResource($href, Stormpath::ACCESS_TOKEN);

    }
}