<?php
namespace Stormpath\Tests\Http\Authc;

use Stormpath\Stormpath;
use Stormpath\Tests\BaseTest;

class RequestSignerTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();
        \Stormpath\Client::tearDown();
    }

    /**
     * @test
     */
    public function it_can_sign_a_request_with_basic_authorization_header()
    {
        \Stormpath\Client::$authenticationScheme = Stormpath::AUTHENTICATION_SCHEME_BASIC;
        $client = \Stormpath\Client::getInstance();

        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\BasicSigner', $client->getDataStore()->getRequestExecutor()->getSigner());

        $directory = $this->createDirectory();

        $this->assertInstanceOf('\\Stormpath\\Resource\\Directory', $directory);

        $directory->delete();



    }

    /**
     * @test
     */
    public function it_can_sign_a_request_with_sauthc1_authorization_header()
    {
        \Stormpath\Client::$authenticationScheme = Stormpath::AUTHENTICATION_SCHEME_SAUTHC1;
        $client = \Stormpath\Client::getInstance();

        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\SAuthc1Signer', $client->getDataStore()->getRequestExecutor()->getSigner());

        $directory = $this->createDirectory();

        $this->assertInstanceOf('\\Stormpath\\Resource\\Directory', $directory);

        $directory->delete();



    }

    private function createDirectory()
    {
        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => 'Main Directory' .md5(time().microtime().uniqid()), 'description' => 'Main Directory description'));
        return self::createResource(\Stormpath\Resource\Directory::PATH, $directory);
    }

}

