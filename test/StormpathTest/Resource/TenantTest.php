<?php

namespace StormpathTest\Resource;

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

class TenantTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    protected function setUp()
    {
        $config = Bootstrap::getApplication()->getConfig();

        $this->assertNull(StormpathService::configure($config['stormpath']['id'], $config['stormpath']['secret']));

        $client = new Client();
        $adapter = new Basic();
        $client->setAdapter($adapter);
        StormpathService::setHttpClient($client);
    }

    public function testGetCurrentTenant()
    {
        $resourceManager = StormpathService::getResourceManager();

        $tenant = $resourceManager->find('Stormpath\Resource\Tenant', 'current');

        $this->assertNotEmpty($tenant->getHref());
        $this->assertNotEmpty($tenant->getName());
        $this->assertNotEmpty($tenant->getKey());
    }

    public function testTenantApplicationsCollection()
    {
        $resourceManager = StormpathService::getResourceManager();

        $tenant = $resourceManager->find('Stormpath\Resource\Tenant', 'current');

        $this->assertGreaterThan(0, sizeof($tenant->getApplications()));
    }

    public function testTenantApplicationTenantIsTenant()
    {
        $resourceManager = StormpathService::getResourceManager();

        $tenant = $resourceManager->find('Stormpath\Resource\Tenant', 'current');

        foreach ($tenant->getApplications() as $application)
        {
            print_r($application->getTenant()->getArrayCopy());die();
        }

    }
}
