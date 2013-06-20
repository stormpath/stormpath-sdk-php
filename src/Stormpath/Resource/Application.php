<?php

 /*
 * @desc Register an application with stormpath
 */
namespace Stormpath\Resource;

use Stormpath\Client\ApiKey;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Json\Json;

class Application
{
    private static $name;
    private static $description;
    private static $status;

    public static function getName()
    {
        return self::$name;
    }

    public static function setName($value)
    {
        self::$name = $value;
    }

    public static function getDescription()
    {
        return self::$description;
    }

    public static function setDescription($value)
    {
        self::$description = $value;
    }

    public static function getStatus()
    {
        return self::$status;
    }

    public static function setStatus($value)
    {
        self::$status = $value;
    }


    public static function registerApplication($apiKey,$options = array())
    {
        if (!$apiKey)
            throw new \Exception('Get an API key');

        $http = new Client();
        $http->setUri('https://api.stormpath.com/v1/applications/' . $apiKey);
        $http->setOptions(array('sslverifypeer' => false));
        $http->setMethod('POST');

        $options['name'] = self::getName();
        $options['description'] = self::getDescription();
        $options['status'] = self::getStatus();

        $response = $http->send();
        return Json::decode($response->getBody());

    }



}


?>
