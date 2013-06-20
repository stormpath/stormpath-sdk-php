<?php

namespace Stormpath\Client;

class ApiKey
{

    private static $id;
    private static  $secret;

    public function getId()
    {
        return self::$id;
    }

    public function setId($value)
    {
        self::$id = $value;
    }

    public function getSecret()
    {
        return self::$secret;
    }

    public function setSecret($value)
    {
        self::$secret = $value;
    }

}