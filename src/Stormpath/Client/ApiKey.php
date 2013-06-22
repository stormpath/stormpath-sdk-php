<?php

namespace Stormpath\Client;



class ApiKey
{

    private static $accessid;
    private static  $secretkey;

    public static function getAccessId()
    {

        return self::$accessid;
    }

    public static function setAccessId($value)
    {
        self::$accessid = $value;
    }

    public static function getSecretkey()
    {

        return self::$secretkey;
    }

    public static function setSecretKey($value)
    {
        self::$secretkey = $value;
    }
}