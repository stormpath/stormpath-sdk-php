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

namespace Stormpath\Provider;

use Stormpath\DataStore\ClassNameResolver;
use Stormpath\DataStore\DefaultClassNameResolver;
use Stormpath\Resource\FacebookProviderData;
use Stormpath\Resource\GithubProviderData;
use Stormpath\Resource\GoogleProviderData;
use Stormpath\Resource\LinkedInProviderData;
use Stormpath\Stormpath;

class ProviderDataClassNameResolver implements ClassNameResolver {

    /**
     * Resolves the class name of a <code>ProviderData</code> child based on the
     * <code>providerId</code> property in the <code>$data</code> object.
     *
     * @param $className the parent class name (should be <code>ProviderData</code>)
     * @param $data the received data object to inspect
     * @param array $options should contain <code>(PropertyBasedClassNameResolver::PROPERTY_ID => ProviderData::PROVIDER_ID)</code> entry
     * @return the <code>ProviderData</code> sub class name according to the given value for <code>ProviderData::PROVIDER_ID</code>
     */
    public function resolve($className, $data, array $options = array())
    {
        assert($className == Stormpath::PROVIDER_DATA, '$className arg should be '.Stormpath::PROVIDER_DATA);

        if (isset($options[DefaultClassNameResolver::PROPERTY_ID]))
        {
            $propertyId = $options[DefaultClassNameResolver::PROPERTY_ID];

            $arrData = json_decode(json_encode($data), true);
            if (isset($arrData[$propertyId]))
            {
                $propertyValue = $arrData[$propertyId];
                switch ($propertyValue)
                {
                    case GoogleProviderData::PROVIDER_ID:
                        return Stormpath::GOOGLE_PROVIDER_DATA;
                    case FacebookProviderData::PROVIDER_ID:
                        return Stormpath::FACEBOOK_PROVIDER_DATA;
                    case GithubProviderData::PROVIDER_ID:
                        return Stormpath::GITHUB_PROVIDER_DATA;
                    case LinkedInProviderData::PROVIDER_ID:
                        return Stormpath::LINKEDIN_PROVIDER_DATA;
                    default:
                        throw new \InvalidArgumentException('Could not find className for providerId '.$propertyValue);
                }

            }
            else
            {
                throw new \InvalidArgumentException('Property '.$propertyId.' is not defined in $data object');
            }
        }
        else
        {
            throw new \InvalidArgumentException('Required key '.DefaultClassNameResolver::PROPERTY_ID.' not found in $options array');
        }
    }
}