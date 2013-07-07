<?php

namespace StormpathTest\Service;

use StormpathTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;
use Stormpath\Service\StormpathService;
use Stormpath\Auth\Digest;
use Stormpath\Auth\Basic;
use Zend\Http\Client;
use Stormpath\Resource\Group;
use Stormpath\Client\ApiKey;

class StormpathServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    protected function setUp()
    {
        $config = Bootstrap::getApplication()->getConfig();

        StormpathService::configure($config['stormpath']['id'], $config['stormpath']['secret']);
        $client = new Client();
        $adapter = new Basic();
        $client->setAdapter($adapter);
        StormpathService::setHttpClient($client);

    }

    public function testConfigureWithBasicAuthentication()
    {
        $randomName = md5(uniqid());

        $result = StormpathService::register($randomName, 'Description', 'enabled');

        $this->assertEquals('ENABLED', $result->status);
        $this->assertEquals($randomName, $result->name);
    }

    public function XtestConfigureWithDigestAuthentication()
    {
        $config = Bootstrap::getApplication()->getConfig();


        $this->assertNull(StormpathService::configure($config['stormpath']['id'], $config['stormpath']['secret']));

        $client = new Client();
        $adapter = new Digest();
        $client->setAdapter($adapter);
        StormpathService::setHttpClient($client);

        $randomName = md5(uniqid());

        $result = StormpathService::register($randomName, 'Description', 'enabled');

        //$this->assertNotEquals('401', $result->status);

        $this->assertEquals('ENABLED', $result->status);
        $this->assertEquals($randomName, $result->name);
    }

    public function testRegister()
    {

    }

    public function testReadGroups()
    {
        $result = Group::read('');

        print_r($result);

        $this->assertEquals('enabled',$result->status);
    }

}
