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

use Stormpath\Resource\InstanceResource;

/** @since 1.13.0 */
class AttributeStatementMappingRules extends InstanceResource
{
    const ITEMS = 'items';


    /**
     * Returns the Set of all AttributeStatementMappingRule objects that indicate how SAML
     * Attribute Statements should populate one or more Stormpath Account field values
     * after a successful SAML login.
     *
     * @since 1.13.0
     * @return array an array of AttributeStatementMappingRule objects
     */
    public function getItems()
    {
        return $this->getProperty(self::ITEMS);
    }

    /**
     * Specifies the Set of all AttributeStatementMappingRule objectss that indicate how
     * SAML Attribute Statements should populate one or more Stormpath Account field
     * values after a successful SAML login.
     *
     * @since 1.13.0
     * @param array $items an array of AttributeStatementMappingRule objects to build a SAML provider.
     * @return self
     */
    public function setItems(array $items)
    {
        $this->setProperty(self::ITEMS, $items);
        return $this;
    }

}