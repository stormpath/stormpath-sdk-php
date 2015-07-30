<?php

namespace Stormpath\Authc\Api;

interface RequestAuthenticator
{
    public function authenticate(Request $request);
}