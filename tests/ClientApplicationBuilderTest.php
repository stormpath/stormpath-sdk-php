<?php


class ClientApplicationBuilderTest extends PHPUnit_Framework_TestCase
{
    private $clientFile = 'client.yml';
    private $applicationHref = 'http://localhost:8080/v1/applications/A0atUpZARYGApaN5f88O3A';
    private $httpPrefix = 'http://';
    private $appHrefWithoutHttp = '@localhost:8080/v1/applications/A0atUpZARYGApaN5f88O3A';
    private $clientBuilder;

    public function setUp()
    {
        $this->clientBuilder = new Services_Stormpath_Client_ClientBuilder('http://localhost:8080/v1');
    }

    public function testReadDefaultProperties()
    {
        $builder = new Services_Stormpath_Client_ClientApplicationBuilder($this->clientBuilder);

        $result = $builder
                  ->setApplicationHref($this->applicationHref)
                  ->setApiKeyFileLocation($this->clientFile)
                  ->build()
                  ->getApplication();

        $className = 'Services_Stormpath_Resource_Application';

        $this->assertInstanceOf($className, $result);
    }
}
