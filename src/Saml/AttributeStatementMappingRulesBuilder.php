<?php

namespace Stormpath\Saml;

class AttributeStatementMappingRulesBuilder
{
    /**
     * @since 1.13.0
     * @var array $attributeStatementMappingRules an array of all AttributeStatementMappingRule objects
     */
    private $attributeStatementMappingRules = [];

    /**
     * Specifies the array of AttributeStatementMappingRule objects for the
     * AttributeStatementMappingRules object, indicating how SAML Attribute
     * Statements should populate one or more Stormpath Account
     * field values after a successful SAML login.
     *
     * @since 1.13.0
     * @param array $attributeStatementMappingRules an array of AttributeStatementMappingRule objects
     * @return AttributeStatementMappingRulesBuilder
     */
    public function setAttributeStatementMappingRules(array $attributeStatementMappingRules)
    {
        foreach($attributeStatementMappingRules as $mappingRule)
        {
            $this->addAttributeStatementMappingRule($mappingRule);
        }

        return $this;
    }

    /**
     * Adds a new AttributeStatementMappingRule to the array of AttributeStatementMappingRule objects,
     * indicating how a SAML Attribute Statement should populate one or more Stormpath Account
     * field values after a successful SAML login.
     *
     * @since 1.13.0
     * @param AttributeStatementMappingRule $attributeStatementMappingRule an instance of
     *                                                                      AttributeStatementMappingRule
     *                                                                      to be added to array of objects
     * @return AttributeStatementMappingRulesBuilder
     */
    public function addAttributeStatementMappingRule(AttributeStatementMappingRule $attributeStatementMappingRule)
    {
        $this->attributeStatementMappingRules[] = $attributeStatementMappingRule;
        return $this;
    }

    /**
     * Builds a new AttributeStatementMappingRules instance based on the state of this builder.
     *
     * @return \Stormpath\Saml\AttributeStatementMappingRules
     */
    public function build()
    {
        $this->attributeStatementMappingRules = array_unique($this->attributeStatementMappingRules, SORT_REGULAR);

        $rules = new \Stormpath\Saml\AttributeStatementMappingRules();
        $rules->setItems($this->attributeStatementMappingRules);
        return $rules;
    }
}