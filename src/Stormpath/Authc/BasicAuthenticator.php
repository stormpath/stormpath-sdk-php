<?php

namespace Stormpath\Authc;

use Stormpath\DataStore\InternalDataStore;
use Stormpath\Service\StormpathService;

class BasicAuthenticator
{

    private $dataStore;

    public function __construct(InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function authenticate($parentHref, AuthenticationRequest $request)
    {
        if (!$parentHref)
        {
            throw new \InvalidArgumentException('$parentHref argument must be specified');
        }

        if (!($request instanceof UsernamePasswordRequest))
        {
            throw new \InvalidArgumentException('Only UsernamePasswordRequest instances are supported.');
        }

        $username = $request->getPrincipals();
        $username = $username ? $username : '';

        $password = $request->getCredentials();
        $password = implode('', $password);

        $value = $username .':' .$password;

        $value = base64_encode($value);

        $attempt = $this->dataStore->instantiate(StormpathService::BASIC_LOGIN_ATTEMPT);
        $attempt->setType('basic');
        $attempt->setValue($value);

        $href = $parentHref . '/loginAttempts';

        return $this->dataStore->create($href, $attempt, StormpathService::AUTHENTICATION_RESULT);
    }
}
