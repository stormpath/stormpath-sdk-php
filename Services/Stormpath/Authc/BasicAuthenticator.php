<?php


class Services_Stormpath_Authc_BasicAuthenticator
{

    private $dataStore;

    public function __construct(Services_Stormpath_DataStore_InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function authenticate($parentHref, Services_Stormpath_Authc_AuthenticationRequest $request)
    {
        if (!$parentHref)
        {
            throw new InvalidArgumentException('$parentHref argument must be specified');
        }

        if (!($request instanceof Services_Stormpath_Authc_UsernamePasswordRequest))
        {
            throw new InvalidArgumentException('Only Services_Stormpath_Authc_UsernamePasswordRequest instances are supported.');
        }

        $username = $request->getPrincipals();
        $username = $username ? $username : '';

        $password = $request->getCredentials();
        $password = implode('', $password);

        $value = $username .':' .$password;

        $value = base64_encode($value);

        $attempt = $this->dataStore->instantiate(Services_Stormpath::BASIC_LOGIN_ATTEMPT, new stdClass());
        $attempt->setType('basic');
        $attempt->setValue($value);

        $href = $parentHref . '/loginAttempts';
        $result = $this->dataStore->create($href, $attempt, Services_Stormpath::AUTHENTICATION_RESULT);

        return $result->getAccount();
    }
}
