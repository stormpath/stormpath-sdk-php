<?php

class ReadTest extends PHPUnit_Framework_TestCase {

    private $client;

    public function setUp() {
        $id = 'id';
        $secret = 'secret';

    }

    public function testClientInstance() {

        $this->assertInstanceOf(ReadTest, $this->client);

    }

}