<?php

namespace Stormpath\Tests\Resource;

use Stormpath\Resource\SamlAttributeStatementMappingRules;
use Stormpath\Resource\SamlProvider;
use Stormpath\Resource\SamlProviderMetadata;
use Stormpath\Tests\TestCase;

/** @group saml */
class SamlProviderTest extends TestCase
{
    /** @test */
    public function all_settable_items_can_be_set_on_the_object_and_is_chainable()
    {
        $samlProvider = new SamlProvider();

        $this->assertInstanceOf('Stormpath\Resource\SamlProvider', $samlProvider->setSsoLoginUrl('something'));
        $this->assertInstanceOf('Stormpath\Resource\SamlProvider', $samlProvider->setSsoLogoutUrl('something'));
        $this->assertInstanceOf('Stormpath\Resource\SamlProvider', $samlProvider->setEncodedX509SigningCert('someCert'));
        $this->assertInstanceOf('Stormpath\Resource\SamlProvider', $samlProvider->setRequestSignatureAlgorithm('RSA-SHA256'));

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

        $this->assertInstanceOf('Stormpath\Resource\SamlProvider', $samlProvider);

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

        $this->assertInstanceOf('Stormpath\Resource\SamlProvider', $provider);
        $this->assertEquals('http://google.com/login', $provider->getSsoLoginUrl());
        $this->assertEquals('http://google.com/logout', $provider->getSsoLogoutUrl());
        $this->assertEquals(self::getDummyCertForSaml(), $provider->getEncodedX509SigningCert());
        $this->assertEquals('RSA-SHA1', $provider->getRequestSignatureAlgorithm());
        $this->assertInstanceOf('Stormpath\Resource\SamlProviderData', $provider->getServiceProviderMetadata());

        $directory->delete();
    }

    /** @test */
    public function getting_without_provider_at_end_of_url_automatically_fixes_itself()
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
        $parts = explode('/', $providerHref);
        array_pop($parts);
        $providerHref = implode('/',$parts);

        $provider = SamlProvider::get($providerHref);

        $this->assertInstanceOf('Stormpath\Resource\SamlProvider', $provider);
        $this->assertEquals('http://google.com/login', $provider->getSsoLoginUrl());
        $this->assertEquals('http://google.com/logout', $provider->getSsoLogoutUrl());
        $this->assertEquals(self::getDummyCertForSaml(), $provider->getEncodedX509SigningCert());
        $this->assertEquals('RSA-SHA1', $provider->getRequestSignatureAlgorithm());
        $this->assertInstanceOf('Stormpath\Resource\SamlProviderData', $provider->getServiceProviderMetadata());

        $directory->delete();
    }

    /** @test */
    public function a_provider_can_get_a_list_of_attribute_statement_mapping_rules()
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

        $mappingRules = $provider->attributeStatementMappingRules;

        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRules', $mappingRules);
        $this->assertInternalType('array', $mappingRules->items);

        $directory->delete();
    }

    /** @test */
    public function a_provider_can_save_an_attribute_statement_mapping_rule()
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

        $ruleBuilder = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();
        $rule = $ruleBuilder->setName('test1')
            ->setAccountAttributes(['customData.test1'])
            ->build();

        $rule2 = $ruleBuilder->setName('test2')
            ->setAccountAttributes(['customData.test2'])
            ->build();


        $rulesBuilder = new \Stormpath\Saml\AttributeStatementMappingRulesBuilder();
        $rulesBuilder->setAttributeStatementMappingRules([$rule, $rule2]);
        $rules = $rulesBuilder->build();

        $provider->setAttributeStatementMappingRules($rules);

        $provider->save();

        $provider = SamlProvider::get($providerHref);

        $mappingRules = $provider->getAttributeStatementMappingRules();

        $this->assertCount(2, $mappingRules->items);

        foreach($mappingRules->items as $item) {
            $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRule', $item);
        }

        $this->assertEquals('test2', $mappingRules->items[1]->name);


        $directory->delete();
    }

//
//    /** @test */
//    public function a_provider_allows_setting_statement_mapping_rules()
//    {
//        $samlProvider = \Stormpath\Resource\SamlProvider::instantiate([
//            'ssoLoginUrl' => 'http://google.com/login',
//            'ssoLogoutUrl' => 'http://google.com/logout',
//            'encodedX509SigningCert' => self::getDummyCertForSaml(),
//            'requestSignatureAlgorithm' => 'RSA-SHA1'
//        ]);
//
//        $directory = \Stormpath\Resource\Directory::create([
//            'name' => makeUniqueName('DirectoryTest samlProvider'),
//            'provider' => $samlProvider
//        ]);
//
//        $mappingRuleBuilder = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();
//        $mappingRule = $mappingRuleBuilder->setName('email')
//            ->setNameFormat('urn:oasis:names:tc:SAML:1.1:nameid-format:email')
//            ->setAccountAttributes([
//                'email',
//                'username'
//            ])
//            ->build();
//
//        $directory->provider->accountMappingRule->save();
//
//
//
//
//
//        $directory->delete();
//    }




}
