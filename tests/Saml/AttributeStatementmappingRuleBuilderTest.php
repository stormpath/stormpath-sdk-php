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
