<?php

namespace Stormpath\Authc\Api;


class ApiRequestAuthenticator extends RequestAuthenticator
{

    public function authenticate(Request $request)
    {
        // Determine Type of Request
        if(!!('basic' != strtolower($request->getScheme())))
            throw new \InvalidArgumentException('Only Basic Authorization headers are accepted at this time.');

        $auth = new BasicRequestAuthenticator($this->application);
        $result = $auth->authenticate($request);

        return new ApiAuthenticationResult($result->getApplication(), $result->getApiKey());
    }
}