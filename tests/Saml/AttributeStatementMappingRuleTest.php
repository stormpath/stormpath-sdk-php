<?php

namespace Stormpath\Tests\Saml;

class AttributeStatementMappingRuleTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function a_built_rule_has_getters_available_to_it()
    {
        $builder = new \Stormpath\Saml\AttributeStatementMappingRuleBuilder();

        $builder->setName('test');
        $builder->setNameFormat('urn:oasis:names:tc:SAML:1.1:nameid-format:email');
        $builder->setAccountAttributes(['email','username']);

        $rule = $builder->build();

        $this->assertEquals('test', $rule->getName());
        $this->assertEquals('urn:oasis:names:tc:SAML:1.1:nameid-format:email', $rule->getNameFormat());
        $this->assertEquals(['email','username'], $rule->getAccountAttributes());
    }

    /** @test */
    public function trying_to_set_extra_properties_throws_exception()
    {
        $this->setExpectedException('BadMethodCallException');

        $rule = new \Stormpath\Saml\AttributeStatementMappingRule('foo','bar',['baz']);
        $rule->something = 'hello';
    }



}
