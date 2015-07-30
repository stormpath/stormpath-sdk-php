<?php
namespace Stormpath\Authc\Api;

use Stormpath\Resource\ApiKey;
use Stormpath\Resource\Application;

class OAuthClientCredentialsAuthenticationResult extends AuthenticatorResult
{
    protected $access_token;

    public function __construct(Application $application, ApiKey $apiKey, $jwt)
    {
        parent::__construct($application,$apiKey);
        $this->access_token = $jwt;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

}