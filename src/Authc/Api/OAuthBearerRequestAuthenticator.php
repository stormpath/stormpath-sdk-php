<?php

namespace Stormpath\Authc\Api;

class OAuthBearerRequestAuthenticator extends InternalRequestAuthenticator implements RequestAuthenticator
{

    public function authenticate(Request $request)
    {
        if (!$this->application)
            throw new \InvalidArgumentException('The application must be set.');

        $apiKey = $this->getApiKeyById($request);

        if($this->isValidApiKey($request, $apiKey))
        {
            $account = $apiKey->account;
        }

        if($this->isValidAccount($account))
        {
            return new OAuthBearerAuthenticationResult($this->application, $apiKey);
        }
    }
}