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

        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\BasicRequestSigner', $client->getDataStore()->getRequestExecutor()->getSigner());

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

        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\SAuthc1RequestSigner', $client->getDataStore()->getRequestExecutor()->getSigner());

        $directory = $this->createDirectory();

        $this->assertInstanceOf('\\Stormpath\\Resource\\Directory', $directory);

        $directory->delete();
    }
    /**
     * @test
     */
    public function it_defaults_to_sauthc1()
    {
        $client = \Stormpath\Client::getInstance();
        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\SAuthc1RequestSigner', $client->getDataStore()->getRequestExecutor()->getSigner());
    }

    /**
     * @test
     */
    public function it_can_set_the_authentication_scheme_from_client_builder()
    {
        $builder = new \Stormpath\ClientBuilder();

        $newClient = $builder->setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
            setApiKeyProperties(\Stormpath\Client::$apiKeyProperties)->
            setAuthenticationScheme(Stormpath::BASIC_AUTHENTICATION_SCHEME)->
            build();


        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\BasicRequestSigner', $newClient->getDataStore()->getRequestExecutor()->getSigner());

    }

    /**
     * @test
     */
    public function it_can_set_authentication_scheme_if_client_called_directly()
    {
        $apiKey = new \Stormpath\ApiKey('id','secret');
        $cacheManager = '\\Stormpath\\Cache\\NullCacheManager';
        $signer = '\\Stormpath\\Http\\Authc\\'.Stormpath::BASIC_AUTHENTICATION_SCHEME.'RequestSigner';
        $client = new \Stormpath\Client($apiKey, $cacheManager, array(), null, new $signer);

        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\BasicRequestSigner', $client->getDataStore()->getRequestExecutor()->getSigner());
        $client->tearDown();
    }

    /**
     * @test
     */
    public function it_will_default_to_suthc1_if_client_called_directly()
    {
        $apiKey = new \Stormpath\ApiKey('id','secret');
        $cacheManager = '\\Stormpath\\Cache\\NullCacheManager';

        $client = new \Stormpath\Client($apiKey, $cacheManager, array());

        $this->assertInstanceOf('\\Stormpath\\Http\\Authc\\SAuthc1RequestSigner', $client->getDataStore()->getRequestExecutor()->getSigner());
        $client->tearDown();
    }

    private function createDirectory()
    {
        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('Directory For Request Signer Test'), 'description' => 'Main Directory description'));
        return self::createResource(\Stormpath\Resource\Directory::PATH, $directory);
    }
}
