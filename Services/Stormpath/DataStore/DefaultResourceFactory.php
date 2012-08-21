<?php

/*
 * Copyright 2012 Stormpath, Inc.
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
class Services_Stormpath_DataStore_DefaultResourceFactory
    implements Services_Stormpath_DataStore_ResourceFactory
{

    private $dataStore;

    const RESOURCE_PATH = 'Services_Stormpath_Resource_';
    const AUTHC_PATH = 'Services_Stormpath_Authc_';

    public function __construct(Services_Stormpath_DataStore_InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function instantiate($className, array $constructorArgs)
    {
        $class = new ReflectionClass($this->qualifyClassName($className));

        array_unshift($constructorArgs, $this->dataStore);

        return $class->newInstanceArgs($constructorArgs);
    }

    private function qualifyClassName($className)
    {
        if (strpos($className, self::RESOURCE_PATH) === false
            and strpos($className, self::AUTHC_PATH) === false)
        {
            return self::RESOURCE_PATH .$className;
        }

        return $className;

    }

}
