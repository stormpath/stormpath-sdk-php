<?php

namespace Stormpath\Resource;

use Stormpath\Stormpath;
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

class ProviderData extends Resource
{
    const PROVIDER_ID   = 'providerId';
    const CREATED_AT    = 'createdAt';
    const MODIFIED_AT   = 'modifiedAt';
    
    public function getProviderId()
    {
        return $this->getProperty(self::PROVIDER_ID);
    }

    public function setProviderId($providerId)
    {
        $this->setProperty(self::PROVIDER_ID, $providerId);
    }

    public function getCreatedAt()
    {
        return $this->getProperty(self::CREATED_AT);
    }
    
    public function setCreatedAt($createdAt)
    {
        $this->setProperty(self::CREATED_AT, $createdAt);
    }

    public function getModifiedAt()
    {
        return $this->getProperty(self::CREATED_AT);
    }
    
    public function setModifiedAt($modifiedAt)
    {
        $this->setProperty(self::CREATED_AT, $modifiedAt);
    }
}