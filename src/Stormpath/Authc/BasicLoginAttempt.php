<?php

namespace Stormpath\Authc;

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

use Stormpath\Resource\Resource;

class BasicLoginAttempt extends Resource
{
    const TYPE = "type";
    const VALUE = "value";

    public function getType()
    {
        return $this->getProperty(self::TYPE);
    }

    public function setType($type)
    {
        $this->setProperty(self::TYPE, $type);
    }

    public function getValue()
    {
        return $this->getProperty(self::VALUE);
    }

    public function setValue($value)
    {
        $this->setProperty(self::VALUE, $value);
    }
}
