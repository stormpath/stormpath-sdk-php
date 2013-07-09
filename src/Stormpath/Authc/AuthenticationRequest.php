<?php

namespace Stormpath\Authc;

interface AuthenticationRequest {

    function getPrincipals();

    function getCredentials();

    function getHost();

    function clear();
}
