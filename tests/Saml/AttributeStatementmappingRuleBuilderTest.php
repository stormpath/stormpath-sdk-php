<?php

namespace Stormpath\Tests\Saml;

/** @group saml */
class AttributeStatementMappingRuleBuilderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function it_allows_chaining_all_methods()
    {
        $builder = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();

        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRuleBuilder', $builder->setName('test'));
        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRuleBuilder', $builder->setNameFormat('urn:oasis:names:tc:SAML:1.1:nameid-format:email'));
        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRuleBuilder', $builder->setAccountAttributes(['email','username']));

    }


    /** @test */
    public function building_returns_an_instance_of_attribute_statement_mapping_rules()
    {
        $builder = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();

        $builder->setName('test');
        $builder->setNameFormat('urn:oasis:names:tc:SAML:1.1:nameid-format:email');
        $builder->setAccountAttributes(['email','username']);

        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRule', $builder->build());


    }


}
