<?php

class Services_Stormpath_Client_ApiKey
{

    private $id;
    private $secret;

    public function __construct($id, $secret) {

        $this->id = $id;
        $this->secret = $secret;
    }


}
