<?php

namespace Stormpath\Authc\Api;


use Stormpath\Resource\Application;

abstract class RequestAuthenticator
{
    protected $application = null;

    public function __construct(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    public abstract function authenticate(Request $request);
}