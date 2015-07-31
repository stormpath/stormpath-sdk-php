<?php

namespace Stormpath\Authc\Api;

use Stormpath\Exceptions\RequestAuthenticatorException;
use Stormpath\Resource\Application;

abstract class InternalRequestAuthenticator {

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param Request $request
     * @return null
     */
    protected function getApiKeyById(Request $request)
    {
        return $this->application->getApiKey($request->getApiId());
    }

    /**
     * @param Request $request
     * @param $apiKey
     * @throws RequestAuthenticatorException
     */
    protected function isValidApiKey(Request $request, $apiKey)
    {
        if ($apiKey->getStatus() == 'DISABLED')
            throw new RequestAuthenticatorException('The API Key is not allowed to make this request.');

        if($request->getScheme() == 'Bearer')
            return true;

        if ($apiKey === null || !!($request->getApiSecret() != $apiKey->getSecret()))
            throw new RequestAuthenticatorException('The API Key is not valid for this request.');

        return true;
    }

    /**
     * @param $account
     * @throws RequestAuthenticatorException
     */
    protected function isValidAccount($account)
    {
        if ($account->getStatus() == 'DISABLED') {
            throw new RequestAuthenticatorException('The Account you are authenticating with is not active.');
        }

        return true;
    }

}