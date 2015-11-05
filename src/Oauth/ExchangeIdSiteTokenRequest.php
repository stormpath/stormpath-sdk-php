<?php


namespace Stormpath\Oauth;


class ExchangeIdSiteTokenRequest
{
    private $grant_type = "id_site_token";
    private $token;

    public function __construct($Token)
    {
        $this->token = $Token;
    }

    public function getToken() {
        return $this->token;
    }

    public function getGrantType() {
        return $this->grant_type;
    }
}