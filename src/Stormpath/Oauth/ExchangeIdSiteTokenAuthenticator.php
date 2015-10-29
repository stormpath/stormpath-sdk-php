<?php


namespace Stormpath\Oauth;


use Stormpath\Resource\Application;

class ExchangeIdSiteTokenAuthenticator
{
    const OAUTH_TOKEN_PATH = "/oauth/token";

    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function authenticate(ExchangeIdSiteTokenRequest $exchangeIdSiteTokenRequest)
    {
        $attempt = new ExchangeIdSiteTokenAttempt();
        $attempt->setGrantType($exchangeIdSiteTokenRequest->getGrantType())
                ->setToken($exchangeIdSiteTokenRequest->getToken());

        $grantResult = $this->application->dataStore->create(
            $this->application->getHref() . self::OAUTH_TOKEN_PATH,
            $attempt,
            \Stormpath\Stormpath::GRANT_AUTHENTICATION_TOKEN
        );

        $builder = new OauthGrantAuthenticationResultBuilder($grantResult);
        return $builder->build();
    }
}