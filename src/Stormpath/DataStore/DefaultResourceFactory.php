<?php

namespace Stormpath\DataStore;

use Guzzle\Common\Exception\InvalidArgumentException;
/*
 * Copyright 2013 Stormpath, Inc.
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
class DefaultResourceFactory implements ResourceFactory
{

    private $dataStore;

    const RESOURCE_PATH = 'Stormpath\Resource\\';

    public function __construct(InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function instantiateChildClass($className, $childClassName, array $constructorArgs)
    {
    	$childClass = new \ReflectionClass($this->qualifyClassName($childClassName));
    	if ($childClass->isSubclassOf($this->qualifyClassName($className)))
    	{
    		return $this->instantiate($childClassName, $constructorArgs);
    	}
    	else
    	{
    		throw new \InvalidArgumentException('Cannot instantiate resource: '.$className.' is not a super class of '.$childClassName);
    	}
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

        return $newClass;
    }

    private function qualifyClassName($className)
    {
        if (strpos($className, self::RESOURCE_PATH) === false)
        {
            return self::RESOURCE_PATH .$className;
        }

        return $className;

    }

}
