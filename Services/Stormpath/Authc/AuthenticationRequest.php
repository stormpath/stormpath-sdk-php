<?php
interface Services_Stormpath_Authc_AuthenticationRequest {

    function getPrincipals();

    function getCredentials();

    function getHost();

    function clear();
}
