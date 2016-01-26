<?php

namespace Stormpath\DataStore;

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
use Stormpath\Saml\AttributeStatementMappingRuleBuilder;
use Stormpath\Saml\AttributeStatementMappingRulesBuilder;

class DefaultResourceFactory implements ResourceFactory
{

    private $dataStore;

    const RESOURCE_PATH = 'Stormpath\Resource\\';

    public function __construct(InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function instantiate($className, array $constructorArgs)
    {

        $class = new \ReflectionClass($this->qualifyClassName($className));

        array_unshift($constructorArgs, $this->dataStore);

        $newClass = $class->newInstanceArgs($constructorArgs);

        if($class->hasConstant ( "CUSTOM_DATA" ))
        {
            $newClass->customData;
        }

        if($newClass instanceof \Stormpath\Resource\SamlProvider) {
            $newClass = $this->convertSamlAttributeStatementMappingRules($newClass);
        }

        return $newClass;
    }

    private function qualifyClassName($className)
    {
        if (class_exists($className) && strstr($className, 'Stormpath')) {
            return $className;
        }

        if (strpos($className, self::RESOURCE_PATH) === false)
        {
            return self::RESOURCE_PATH .$className;
        }

        return $className;

    }

    private function convertSamlAttributeStatementMappingRules($newClass)
    {
        $mappingRules = $newClass->getAttributeStatementMappingRules();
        if(null === $mappingRules) {
            return $newClass;
        }

        $items = $mappingRules->getItems();
        $newItems = [];

        $itemBuilder = new AttributeStatementMappingRuleBuilder();
        foreach($items as $item) {
            $newItems[] = $itemBuilder->setName($item->name)
                ->setNameFormat($item->nameFormat)
                ->setAccountAttributes($item->accountAttributes)
                ->build();
        }

        $newClass->getAttributeStatementMappingRules()->items = $newItems;
        return $newClass;
    }

}
