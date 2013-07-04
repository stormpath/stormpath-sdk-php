<?php
/**
 * Getters and Setters for the API Key
 */

namespace Stormpath\Client;

class ApiKey
{

    private static $id;
    private static $secret;

    public static function getId()
    {
        return self::$id;
    }

    private static function setId($value)
    {
        self::$id = $value;
    }

    public static function getSecret()
    {

        return self::$secret;
    }

    private static function setSecret($value)
    {
        self::$secret = $value;
    }

}