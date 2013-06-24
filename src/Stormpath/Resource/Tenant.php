<?php
/**
 * A class to fetch the Tenant resources
 *
 */

namespace Stormpath\Resource;

use Stormpath\Client\ApiKey;
use Stormpath\Service\StormpathService;
use Zend\Http\Client;
use Zend\Json\Json;

class Tenant
{
    private static $name;

    public static function getName()
    {

        return self::$name;
    }

    public static function setName($value)
    {
        self::$name = $value;
    }

    public static function configure($name)
    {
        self::setName($name);
    }

    /*
     * $method => GET to read the tenant resources ,POST to update the tenant resources
     * $current => set to true to get the current tenant resources else set to false
     */

    public static function Tenant($method, $current, $options = array())
    {
        if (!ApiKey::getAccessId())
            throw new \Exception('Get an API key');

        $http = new Client();

        if($current)
        {
            $http->setUri(StormpathService::BASEURI .'/tenants/current/'. ApiKey::getAccessId());
        }
        else
        {
            $http->setUri(StormpathService::BASEURI .'/tenants/'. ApiKey::getAccessId());
        }
        $http->setOptions(array('sslverifypeer' => false));
        $http->setMethod($method);

        $options['name'] = self::getName();
        $http->setParameterGet($options);

        $response = $http->send();
        return Json::decode($response->getBody());
    }

}