<?php

class ReadTest extends PHPUnit_Framework_TestCase {

    private $client;

    public function setUp() {

        $apiKey = new Services_Stormpath_Client_ApiKey('id', 'secret');
        $this->client = new Services_Stormpath_Client_Client($apiKey);

    }

    public function testClientInstance() {

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $this->client);

    }

}