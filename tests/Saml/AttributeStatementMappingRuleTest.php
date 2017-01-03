<?php
/**
 * Copyright 2017 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Stormpath\Tests\Saml;

/** @group saml */
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
