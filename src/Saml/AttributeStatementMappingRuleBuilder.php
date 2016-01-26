<?php
/*
 * Copyright 2016 Stormpath, Inc.
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
 */

namespace Stormpath\Saml;

/** @since 1.13.0 */
class AttributeStatementMappingRuleBuilder
{
    private $name;

    private $nameFormat;

    private $accountAttributes;

    /**
     * Sets the SAML Attribute name, that when encountered, should have its value applied
     * as Account field values. When this name is encountered when processing a SAML
     * Attribute Statement, its associated value will be set as the value for all
     * Stormpath Account field names specified in the accountAttributes.
     *
     * @since 1.13.0
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the format for the SAML Attribute.
     * Examples of valid formats are:
     *  urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
     *  urn:oasis:names:tc:SAML:2.0:nameid-format:persistent
     *  urn:oasis:names:tc:SAML:2.0:nameid-format:transient
     *  urn:oasis:names:tc:SAML:2.0:attrname-format:basic
     *  urn:oasis:names:tc:SAML:2.0:nameid-format:entity
     *
     * @since 1.13.0
     * @param string $nameFormat
     * @return self
     */
    public function setNameFormat($nameFormat)
    {
        $this->nameFormat = $nameFormat;
        return $this;
    }

    /**
     * Sets the Stormpath account fields that should be updated when encountering
     * SAML Attribute name.  If discovered, that SAML Attribute value will be
     * set on all of the Stormpath account fields named in this collection.
     *
     * @since 1.13.0
     * @param array $accountAttributes
     * @return self
     */
    public function setAccountAttributes(array $accountAttributes)
    {
        $this->accountAttributes = $accountAttributes;
        return $this;
    }

    /**
     * Builds a new AttributeStatementMappingRule based on the current state of this builder.
     *
     * @since 1.13.0
     * @return AttributeStatementMappingRule a new AttributeStatementMappingRule to be included in the AttributeStatementMappingRules for a Saml Provider.
     */
    public function build()
    {
        return new AttributeStatementMappingRule($this->name, $this->nameFormat, $this->accountAttributes);
    }

}