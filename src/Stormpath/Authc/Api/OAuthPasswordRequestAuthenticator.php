<?php


namespace Stormpath\Authc\Api;


use Stormpath\Resource\OauthRequest;
use Stormpath\Resource\OauthToken;

class OAuthPasswordRequestAuthenticator extends InternalRequestAuthenticator
{
    public function authenticate($user, $password)
    {
        $oauthEndpoint = $this->application->oauthPolicy->tokenEndpoint->href;

        $request = (new OauthRequest())
                    ->setGrantType('password')
                    ->setUsername($user)
                    ->setPassword($password);

        $result = $this->application->dataStore->create($oauthEndpoint, $request ,'OauthToken', ['grant_type'=>'password', 'username'=>$user, 'password'=>$password]);
        var_dump($result);
    }
}

