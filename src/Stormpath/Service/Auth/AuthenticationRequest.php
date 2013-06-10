<?php

namespace Stormpath\Auth;

interface AuthenticationRequest
{
    function getPrincipals();
    function getCredentials();
    function getHost();
    function clear();

}
?>
