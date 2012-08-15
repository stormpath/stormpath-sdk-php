<?php


class WriteTest extends PHPUnit_Framework_TestCase
{
    private $client;

    public function setUp() {

        $this->client = Services_Stormpath::createClient('id',
            'secret',
            'http://localhost:8080/v1');

    }

    public function testSuccessfulAuthentication()
    {
        $href = 'applications/A0atUpZARYGApaN5f88O3A';
        $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

        $result = $application->authenticate(new Services_Stormpath_Authc_UsernamePasswordRequest('kentucky', 'super_P4ss'));

        $className = 'Services_Stormpath_Resource_Account';
        $this->assertInstanceOf($className, $result);
    }

    public function testFailedAuthentication()
    {
        $href = '/applications/A0atUpZARYGApaN5f88O3A';
        $application = $this->client->getDataStore()->getResource($href, Services_Stormpath::APPLICATION);

        $result = false;
        try {

            $result = $application->authenticate(new Services_Stormpath_Authc_UsernamePasswordRequest('kentucky', 'badPass'));

        } catch (Services_Stormpath_Resource_ResourceError $re)
        {
            $this->assertInternalType('int', $re->getStatus());
            $this->assertInternalType('int', $re->getErrorCode());
            $this->assertInternalType('string', $re->getDeveloperMessage());
            $this->assertInternalType('string', $re->getMoreInfo());
            $this->assertInternalType('string', $re->getMessage());
            $result = true;
        }

        $this->assertTrue($result);
    }

}
