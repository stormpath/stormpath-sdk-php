<?php
namespace Stormpath\Tests\Http\Authc;

use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class RequestSignerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        \Stormpath\Client::tearDown();
    }

    public function tearDown()
    {
        \Stormpath\Client::tearDown();
        parent::tearDown();
    }
    /**
     * @test
     */
    public function it_can_sign_a_request_with_basic_authorization_header()
    {
        \Stormpath\Client::$authenticationScheme = Stormpath::BASIC_AUTHENTICATION_SCHEME;
        $client = \Stormpath\Client::getInstance();

        $directory = $this->createDirectory();

        $this->assertInstanceOf('\\Stormpath\\Resource\\Directory', $directory);

        $directory->delete();
    }
    /**
     * @test
     */
    public function it_can_sign_a_request_with_sauthc1_authorization_header()
    {
        \Stormpath\Client::$authenticationScheme = Stormpath::SAUTHC1_AUTHENTICATION_SCHEME;
        $client = \Stormpath\Client::getInstance();

        $directory = $this->createDirectory();

        $this->assertInstanceOf('\\Stormpath\\Resource\\Directory', $directory);

        $directory->delete();
    }

    private function createDirectory()
    {
        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('Directory For Request Signer Test'), 'description' => 'Main Directory description'));
        return self::createResource(\Stormpath\Resource\Directory::PATH, $directory);
    }
}
