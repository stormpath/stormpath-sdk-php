<?php

namespace Stormpath\Authc\Api;


use Exception;
use Stormpath\Exceptions\AuthenticatorException;
use Stormpath\Resource\Account;
use Stormpath\Resource\ApiKey;
use Stormpath\Resource\Application;
use Stormpath\Resource\ResourceError;

class BasicRequestAuthenticator implements RequestAuthenticator
{

    protected $application = null;

    public function __construct(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    public function authenticate(Request $request)
    {
        if (!$this->application)
            throw new \InvalidArgumentException('The application must be set.');

        $apiKey = $this->application->getApiKey($request->getApiId());

        if ($apiKey === null || !!($request->getApiSecret() != $apiKey->getSecret()))
            throw new AuthenticatorException('The API Key is not valid for this request.');

        if ($apiKey->getStatus() == 'DISABLED')
            throw new AuthenticatorException('The API Key is not allowed to make this request');

        $account = $apiKey->account;

        if ($account->getStatus() == 'DISABLED') {
            throw new AuthenticatorException('The Account you are authenticating with is not active.');
        }

        return new AuthenticationResult($this->application, $apiKey);


    }

}