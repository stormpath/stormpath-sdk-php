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

namespace Stormpath\Authc\Api;

use Stormpath\Exceptions\RequestAuthenticatorException;

class ApiRequestAuthenticator extends InternalRequestAuthenticator implements RequestAuthenticator
{

    public function authenticate(Request $request)
    {
        $authenticator = null;

        if ($request->hasAuthorizationHeader()) {
            if ($request->isBasicAuthorization()) {
                if ($request->hasGrantType()) {
                    $authenticator = new OAuthClientCredentialsRequestAuthenticator($this->application);
                } else {
                    $authenticator = new BasicRequestAuthenticator($this->application);
                }
            } else if ($request->isBearerAuthorization()) {
                $authenticator = new OAuthBearerRequestAuthenticator($this->application);
            }

        }

        if($authenticator) {
            $result = $authenticator->authenticate($request);
            $application = $result->getApplication();
            $apiKey = $result->getApiKey();
            $accessToken = null;
            if(method_exists($result, 'getAccessToken')) {
                $accessToken = $result->getAccessToken();
            }

            return new ApiAuthenticationResult($application, $apiKey, $accessToken);
        }
        throw new RequestAuthenticatorException('The method of authentication you are trying is not an allowed method.
                                                 Please make sure you are using one of the following methods for
                                                 Authentication: Basic, OAuth Bearer, or OAuth Client Credentials.');
    }
}