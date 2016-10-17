<?php
/**
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
 *
 */

namespace Stormpath\Mfa;

use Stormpath\Resource\AbstractCollectionResource;
use Stormpath\Stormpath;

class FactorList extends AbstractCollectionResource
{
    protected function toResource($className, \stdClass $properties)
    {
        switch(strtolower($properties->type)) {
            case 'sms' :
                return $this->dataStore->instantiate(Stormpath::SMS_FACTOR, $properties);
            case 'google-authenticator' :
                return $this->dataStore->instantiate(Stormpath::GOOGLE_AUTHENTICATOR_FACTOR, $properties);
        }

        return $this->dataStore->instantiate($className, $properties);

    }

    function getItemClassName()
    {
        return Stormpath::FACTOR;
    }
}