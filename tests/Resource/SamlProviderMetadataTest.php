<?php

namespace Stormpath\Tests\Resource;

use Stormpath\Resource\SamlProvider;
use Stormpath\Tests\TestCase;

/** @group saml */
class SamlProviderMetadataTest extends TestCase
{
    private static $samlProvider;

    private static $directory;

    private static $directoryId;

    private static $metadata;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$samlProvider = \Stormpath\Resource\SamlProvider::instantiate([
            'ssoLoginUrl' => 'http://google.com/login',
            'ssoLogoutUrl' => 'http://google.com/logout',
            'encodedX509SigningCert' => self::getDummyCertForSaml(),
            'requestSignatureAlgorithm' => 'RSA-SHA1'
        ]);

        self::$directory = \Stormpath\Resource\Directory::create([
            'name' => makeUniqueName('DirectoryTest samlProvider'),
            'provider' => self::$samlProvider
        ]);

        $parts = explode('/', self::$directory->href);
        self::$directoryId = end($parts);

        self::$metadata = SamlProvider::get(self::$directory->provider->href)->serviceProviderMetadata;

    }

    /** @test */
    public function it_can_get_the_entity_id()
    {
        $this->assertEquals('urn:stormpath:directory:'.self::$directoryId.':provider:sp', self::$metadata->getEntityId());
    }

    /** @test */
    public function it_will_get_the_x509_cert()
    {
        $cert = self::$metadata->getX509SigningCert();

        $this->assertInstanceOf('Stormpath\Resource\X509SigningCert', $cert);
    }

    /** @test */
    public function it_will_get_the_assertion_consumer_service_post_endpoint()
    {
        $servicePostEndpoint = self::$metadata->getAssertionConsumerServicePostEndpoint();

        $this->assertInstanceOf('Stormpath\Resource\AssertionConsumerServicePostEndpoint', $servicePostEndpoint);
    }



    public static function tearDownAfterClass()
    {
        self::$directory->delete();
        parent::tearDownAfterClass();
    }




}
