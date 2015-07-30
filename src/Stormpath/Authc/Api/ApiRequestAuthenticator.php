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
            return $authenticator->authenticate($request);
        }
        throw new RequestAuthenticatorException('The method of authentication you are trying is not an allowed method.
                                                 Please make sure you are using one of the following methods for
                                                 Authentication: Basic, OAuth Bearer, or OAuth Client Credentials.');
    }
}