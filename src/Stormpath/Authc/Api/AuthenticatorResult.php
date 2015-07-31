<?php

namespace Stormpath\Authc\Api;

use Stormpath\Resource\ApiKey;
use Stormpath\Resource\Application;

class AuthenticatorResult
{

    protected $application;

    protected $apiKey;

    protected $accessToken;

    public function __construct(Application $application, ApiKey $apiKey, $accessToken = null)
    {
        $this->application = $application;

        $this->apiKey = $apiKey;

        if($accessToken) {
            $this->accessToken = $accessToken;
        }
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
