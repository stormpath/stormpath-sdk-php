<?php
 /*
 * @desc Register an application with Stormpath
 */

namespace Stormpath\Resource;

use Stormpath\Client\ApiKey;
use Stormpath\Service\StormpathService;
use Zend\Http\Client;
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

    public static function configure($name, $status, $description)
    {
        self::setName($name);
        self::setDescription($description);
        self::setStatus($status);
    }

    public static function registerApplication($options = array())
    {
        if (!ApiKey::getAccessId())
            throw new \Exception('Get an API key');

        $http = new Client();
        $http->setUri(StormpathService::BASEURI .'/applications/'. ApiKey::getAccessId());
        $http->setOptions(array('sslverifypeer' => false));
        $http->setMethod('POST');

        $options['name'] = self::getName();
        $options['description'] = self::getDescription();
        $options['status'] = self::getStatus();
        $http->setParameterGet($options);

        $response = $http->send();
        return Json::decode($response->getBody());

    }

}
