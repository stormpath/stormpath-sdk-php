<?php

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