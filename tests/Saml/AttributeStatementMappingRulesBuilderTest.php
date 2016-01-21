<?php

namespace Stormpath\Tests\Saml;

class AttributeStatementMappingRulesBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function builder_is_returned_after_setting_a_set_of_rules()
    {
        $builder = new \Stormpath\Saml\AttributeStatementMappingRulesBuilder();

        $rule = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();

        $rule->setName('test');
        $rule->setNameFormat('urn:oasis:names:tc:SAML:1.1:nameid-format:email');
        $rule->setAccountAttributes(['email','username']);
        $rules = $rule->build();

        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRulesBuilder', $builder->setAttributeStatementMappingRules([$rules]));

    }

    /** @test */
    public function builder_is_returned_after_setting_a_rule()
    {
        $builder = new \Stormpath\Saml\AttributeStatementMappingRulesBuilder();

        $rule = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();

        $rule->setName('test');
        $rule->setNameFormat('urn:oasis:names:tc:SAML:1.1:nameid-format:email');
        $rule->setAccountAttributes(['email','username']);
        $rules = $rule->build();

        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRulesBuilder', $builder->addAttributeStatementMappingRule($rules));

    }

    /** @test */
    public function building_a_set_of_rules_returns_attribute_statement_mapping_rules()
    {
        $builder = new \Stormpath\Saml\AttributeStatementMappingRulesBuilder();

        $ruleBuilder = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();

        $ruleBuilder->setName('test');
        $ruleBuilder->setNameFormat('urn:oasis:names:tc:SAML:1.1:nameid-format:email');
        $ruleBuilder->setAccountAttributes(['email','username']);
        $rule = $ruleBuilder->build();

        $rules = $builder->addAttributeStatementMappingRule($rule)->build();

        $this->assertInstanceOf('Stormpath\Saml\AttributeStatementMappingRules', $rules);
    }

}
