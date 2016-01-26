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


use Stormpath\Resource\Application;

class RefreshGrantAuthenticator
{
    const OAUTH_TOKEN_PATH = "/oauth/token";

    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function authenticate(RefreshGrantRequest $refreshGrantRequest)
    {
        $attempt = new RefreshGrantAuthenticationAttempt();
        $attempt->setGrantType($refreshGrantRequest->getGrantType())
                ->setRefreshToken($refreshGrantRequest->getRefreshToken());

        $grantResult = $this->application->dataStore->create(
            $this->application->getHref() . self::OAUTH_TOKEN_PATH,
            $attempt,
            \Stormpath\Stormpath::GRANT_AUTHENTICATION_TOKEN
        );

        $builder = new OauthGrantAuthenticationResultBuilder($grantResult);
        $builder->setIsRefreshAuthGrantRequest(true);
        return $builder->build();
    }
}