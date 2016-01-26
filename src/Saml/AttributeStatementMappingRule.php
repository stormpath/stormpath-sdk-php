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
class AttributeStatementMappingRule
{

    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $nameFormat;

    /**
     * @var
     */
    public $accountAttributes;

    public function __construct($name, $nameFormat, array $accountAttributes)
    {
        $this->name = $name;
        $this->nameFormat = $nameFormat;
        $this->accountAttributes = $accountAttributes;
    }

    /**
     * Returns the SAML Attribute name, that when encountered, should have its value applied as Account field values.
     * When this name is encountered when processing a SAML Attribute Statement, its associated value will be set as the
     * value for all Stormpath Account field names specified in the
     * accountAttributes collection.
     *
     * @since 1.13.0
     * @return string SAML Attribute name, should have its value set on the specified Account fields.
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Returns the format for the SAML Attribute.
     *
     * @since 1.13.0
     * @return string SAML format for the SAML Attribute.
     */
    public function getNameFormat()
    {
        return $this->nameFormat;
    }

    /**
     * Returns the Stormpath account fields that should be updated for the
     * SAML Attribute name.  If discovered, that SAML Attribute value will
     * be set on all of the Stormpath account fields named in this array.
     *
     * @since 1.13.0
     * @return array Stormpath account fields that should be updated.
     */
    public function getAccountAttributes()
    {
        return $this->accountAttributes;
    }

    /**
     * Prevents setting random values on the class.  We only need name, nameFormat, and accountAttributes
     *
     * @since 1.13.0
     * @param $attribute
     * @param $value
     * @throws \BadMethodCallException
     */
    public function __set($attribute, $value)
    {
        throw new \BadMethodCallException('You can not set properties directly on this class. Please use the Builder');
    }


}