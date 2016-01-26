<?php

namespace Stormpath\Util;

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

class Magic {

    /** @var array Hash of methods available to the class (provides fast isset() lookups) */
    private  $methods;

    public function __construct()
    {
        $this->methods = array_flip(get_class_methods(get_class($this)));
    }

    /**
     * Magic "get" method
     *
     * @param string $property Property name
     * @return mixed|null Property value if it exists, null if not
     */
    public function __get($property)
    {
        $method = 'get' .ucfirst($property);
        if (isset($this->methods[$method])) {
            return $this->{$method}();
        }

        return null;
    }

    /**
     * Magic "set" method
     *
     * @param string $property Property name
     * @param mixed $value Property value
     */
    public function __set($property, $value)
    {
        $method = 'set' .ucfirst($property);
        if (isset($this->methods[$method])) {
            $this->{$method}($value);
        }
    }
}