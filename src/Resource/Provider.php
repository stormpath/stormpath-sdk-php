<?php

namespace Stormpath\Resource;

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

class Provider extends Resource
{
    const CREATED_AT    = "createdAt";
    const MODIFIED_AT   = "modifiedAt";
    const PROVIDER_ID   = "providerId";

    const PATH          = "provider";

    /**
     * Returns the provider's created date.
     *
     * @return the provider's created date.
     */
    public function getCreatedAt()
    {
        return $this->getProperty(self::CREATED_AT);
    }

    /**
     * Returns the provider's last modification date
     *
     * @return the provider's last modification date
     */
    public function getModifiedAt()
    {
        return $this->getProperty(self::MODIFIED_AT);
    }

    /**
     * Getter for the Stormpath ID of the Provider (e.g. "facebook" or "google").
     *
     * @return the Stormpath ID of the Provider.
     */
    public function getProviderId()
    {
        return $this->getProperty(self::PROVIDER_ID);
    }
}