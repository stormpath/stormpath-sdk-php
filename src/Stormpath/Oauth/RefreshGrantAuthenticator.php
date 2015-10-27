<?php


namespace Stormpath\Oauth;


use Stormpath\Resource\Application;

class RefreshGrantAuthenticator
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

    public function authenticate(RefreshGrantRequest $refreshGrantRequest)
    {
        $attempt = new RefreshGrantAuthenticationAttempt();
        $attempt->setGrantType($refreshGrantRequest->getGrantType())
                ->setRefreshToken($refreshGrantRequest->getRefreshToken());

        $grantResult = $this->application->dataStore->create(
            $this->application->getHref() . self::OAUTH_TOKEN_PATH,
            $attempt,
            \Stormpath\Stormpath::GRANT_AUTHENTICATION_TOKEN
        );

        $builder = new OauthGrantAuthenticationResultBuilder($grantResult);
        $builder->setIsRefreshAuthGrantRequest(true);
        return $builder->build();
    }
}