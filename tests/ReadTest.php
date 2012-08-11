<?php
require_once 'HTTP/Request2.php';

class ReadTest extends PHPUnit_Framework_TestCase {

    private $client;

    public function setUp() {

        $apiKey = new Services_Stormpath_Client_ApiKey('id', 'secret');
        $this->client = new Services_Stormpath_Client_Client($apiKey, 'http://localhost:8080/v1');

    }

    public function testGetTenant() {

        $tenantClassName = 'Services_Stormpath_Resource_Tenant';
        $tenant = $this->client->getCurrentTenant();

        $this->assertInstanceOf($tenantClassName, $tenant);
        $this->assertInternalType('string', $tenant->getName());
        $this->assertInternalType('string', $tenant->getKey());

    }

}