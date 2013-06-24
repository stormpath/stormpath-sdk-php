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
use Stormpath\Http\Client\Adapter\Digest;
use Stormpath\Http\Client\Adapter\Basic;
use Zend\Http\Client;

class StormpathServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    protected function setUp()
    {
    }

    public function testConfigureWithBasicAuthentication()
    {
        $config = Bootstrap::getApplication()->getConfig();


        $this->assertNull(StormpathService::configure($config['stormpath']['id'], $config['stormpath']['secret']));

        $client = new Client();
        $adapter = new Basic();
        $client->setAdapter($adapter);
        StormpathService::setHttpClient($client);

        $randomName = md5(uniqid());

        $result = StormpathService::register($randomName, 'Description', 'enabled');

        $this->assertEquals('ENABLED', $result->status);
        $this->assertEquals($randomName, $result->name);
    }

    public function testConfigureWithDigestAuthentication()
    {
        $config = Bootstrap::getApplication()->getConfig();


        $this->assertNull(StormpathService::configure($config['stormpath']['id'], $config['stormpath']['secret']));

        $client = new Client();
        $adapter = new Digest();
        $client->setAdapter($adapter);
        StormpathService::setHttpClient($client);

        $randomName = md5(uniqid());

        $result = StormpathService::register($randomName, 'Description', 'enabled');

        $this->assertEquals('ENABLED', $result->status);
        $this->assertEquals($randomName, $result->name);
    }

}
