<?php

namespace Stormpath\Tests\Authc\Api;

use Stormpath\Authc\Api\Request;
use Stormpath\Tests\BaseTest;

class RequestTest extends BaseTest
{

    public function tearDown()
    {
        Request::tearDown();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage You need to supply the authorization header as part of your request.
    Please add the header and try again.
     */
    public function it_throws_exception_if_authorization_header_is_not_set()
    {
        Request::createFromGlobals();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage It seems your Authorization header is formatted incorrectly. Please
    ensure it is formatted correctly and try again.
     */
    public function it_throws_exception_if_authorization_header_is_not_formatted_correctly()
    {
        $_REQUEST['Authorization'] = 'InvalidAuth:Header';
        Request::createFromGlobals();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage It appears the Authorization header is not formatted correctly.
     */
    public function it_throws_exception_if_decoded_auth_token_is_not_formatted_correctly()
    {
        $_REQUEST['Authorization'] = 'Basic testme';
        Request::createFromGlobals();
    }

    /**
     * @test
     */
    public function it_successfully_decodes_authorization_header()
    {
        $header = base64_encode('123:abc');
        $_REQUEST['Authorization'] = "Basic $header";
        $request = Request::createFromGlobals();

        $this->assertEquals('123', $request->getApiId());
        $this->assertEquals('abc', $request->getApiSecret());
        $this->assertEquals('Basic', $request->getScheme());


        $this->assertCount(2, $request->getSchemeAndValue());


    }

}