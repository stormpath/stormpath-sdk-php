<?php
 /*
 * @desc Register an application with Stormpath
 */

namespace Stormpath\Resource;

use Stormpath\Resource\AbstractResource;
use Stormpath\Service\StormpathService;
use Zend\Http\Client;
use Zend\Json\Json;

class Application extends AbstractResource
{
    protected $name;
    protected $description;
    protected $status;

    public static function getName()
    {
        $this->_load();
        return $this->name;
    }

    public static function setName($value)
    {
        $this->_load();
        $this->name = $value;
        return $this;
    }

    public static function getDescription()
    {
        $this->_load();
        return $this->description;
    }

    public static function setDescription($value)
    {
        $this->_load();
        $this->description = $value;
        return $this;
    }

    public static function getStatus()
    {
        $this->_load();
        return $this->status;
    }

    public static function setStatus($value)
    {
        $this->_load();
        $this->status = $value;
        return $this;
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
