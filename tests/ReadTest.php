<?php
require_once 'HTTP/Request2.php';

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

    public function testGetRequest() {

        $request = new HTTP_Request2('http://localhost:8080/v1/tenants/current');
        $request->setMethod(HTTP_Request2::METHOD_GET);
        $request->setAuth('id', 'secret');
        $response = $request->send();

        echo json_decode($response->getBody())->{'href'};
    }

    public function testCreateInstance(){

        $result = $this->createInstance('Request2', array('http://localhost:8080/v1?q=bla', 'bla'));

        $this->assertInstanceOf('HTTP_Request2', $result);
    }

    private function createInstance($className, array $params){

        $class = new ReflectionClass('HTTP_' .$className);

        return $class->newInstanceArgs($params);

    }

}