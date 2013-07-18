<?php
/**
 *
 */

namespace Stormpath\Authc;

interface AuthenticationRequest
{
    public function getPrincipals();

    public function getCredentials();

    public function getHost();

    public function clear();
}