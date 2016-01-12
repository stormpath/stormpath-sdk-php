<?php

namespace Stormpath\Tests\Resource;

use Stormpath\Resource\SamlProvider;
use Stormpath\Resource\SamlProviderMetadata;
use Stormpath\Tests\TestCase;

class SamlProviderTest extends TestCase
{
    /** @test */
    public function all_settable_items_can_be_set_on_the_object()
    {
        $samlProvider = new SamlProvider();

        $this->assertInstanceOf(SamlProvider::class, $samlProvider->setSsoLoginUrl('something'));
        $this->assertInstanceOf(SamlProvider::class, $samlProvider->setSsoLogoutUrl('something'));
        $this->assertInstanceOf(SamlProvider::class, $samlProvider->setEncodedX509SigningCert('someCert'));
        $this->assertInstanceOf(SamlProvider::class, $samlProvider->setRequestSignatureAlgorithm('RSA-SHA256'));

    }

    /** @test */
    public function items_that_are_settable_can_be_retreived()
    {
        $samlProvider = new SamlProvider();

        $samlProvider->setSsoLoginUrl('something');
        $samlProvider->setSsoLogoutUrl('something');
        $samlProvider->setEncodedX509SigningCert('someCert');
        $samlProvider->setRequestSignatureAlgorithm('RSA-SHA256');

        $this->assertEquals('something', $samlProvider->getSsoLoginUrl());
        $this->assertEquals('something', $samlProvider->getSsoLogoutUrl());
        $this->assertEquals('someCert', $samlProvider->getEncodedX509SigningCert());
        $this->assertEquals('RSA-SHA256', $samlProvider->getRequestSignatureAlgorithm());

    }

    /** @test */
    public function instantiating_the_object_with_the_static_call_returns_an_instance_of_saml_provider()
    {
        $samlProvider = SamlProvider::instantiate([
            'ssoLoginUrl' => 'http://google.com/login',
            'ssoLogoutUrl' => 'http://google.com/logout',
            'encodedX509SigningCert' => 'SomethingElse',
            'requestSignatureAlgorithm' => 'RSA-SHA256'
        ]);

        $this->assertInstanceOf(SamlProvider::class, $samlProvider);

        $this->assertEquals('http://google.com/login', $samlProvider->getSsoLoginUrl());
        $this->assertEquals('http://google.com/logout', $samlProvider->getSsoLogoutUrl());
        $this->assertEquals('SomethingElse', $samlProvider->getEncodedX509SigningCert());
        $this->assertEquals('RSA-SHA256', $samlProvider->getRequestSignatureAlgorithm());


    }
    
    /** @test */
    public function a_saml_provider_can_be_retreived()
    {
        $samlProvider = \Stormpath\Resource\SamlProvider::instantiate([
            'ssoLoginUrl' => 'http://google.com/login',
            'ssoLogoutUrl' => 'http://google.com/logout',
            'encodedX509SigningCert' => self::getDummyCertForSaml(),
            'requestSignatureAlgorithm' => 'RSA-SHA1'
        ]);

        $directory = \Stormpath\Resource\Directory::create([
            'name' => makeUniqueName('DirectoryTest samlProvider'),
            'provider' => $samlProvider
        ]);

        $providerHref = $directory->provider->href;

        $provider = SamlProvider::get($providerHref);

        $this->assertInstanceOf(SamlProvider::class, $provider);
        $this->assertEquals('http://google.com/login', $provider->getSsoLoginUrl());
        $this->assertEquals('http://google.com/logout', $provider->getSsoLogoutUrl());
        $this->assertEquals(self::getDummyCertForSaml(), $provider->getEncodedX509SigningCert());
        $this->assertEquals('RSA-SHA1', $provider->getRequestSignatureAlgorithm());
        $this->assertInstanceOf(SamlProviderMetadata::class, $provider->getServiceProviderMetadata());

        $directory->delete();
    }


}
