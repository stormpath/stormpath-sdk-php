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

namespace Stormpath\DataStore;

use Stormpath\Provider\ProviderDataClassNameResolver;
use Stormpath\Stormpath;

/**
 * Helper for the <code>DataStore</code> to resolve the class name of a resource
 * to instantiate based on the value of a property in the data set received.
 *
 * @package Stormpath\DataStore
 */
class DefaultClassNameResolver implements ClassNameResolver
{

    const PROPERTY_ID = "propertyId";

    private static $instance;
    private $delegates;

    private function __construct()
    {
        $this->delegates = array(
            Stormpath::PROVIDER_DATA => new ProviderDataClassNameResolver()
        );
    }

    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Resolves a className to instantiate based on a property value that comes in the
     * received data set. The class to instantiate is a child in the class hierarchy of
     * the given <code>$className</code>.
     *
     * @param $className hierarchy parent of the child class that is getting resolved.
     * @param $data the received data set.
     * @param array $options contains the name of the property (<code>propertyId</code>)
     * that is going to be used to resolve the child class to instantiate.
     * @return resolved className
     */
    public function resolve($className, $data, array $options = array())
    {
        if (isset($options[DefaultClassNameResolver::PROPERTY_ID]))
        {
            if (isset($this->delegates[$className]))
            {
                $classNameResolver = $this->delegates[$className];
                return $classNameResolver->resolve($className, $data, $options);
            }
            else
            {
                throw new \InvalidArgumentException("No delegate resolver found for className ".$className);
            }

        }
        else
        {
            return $className;
        }
    }
}