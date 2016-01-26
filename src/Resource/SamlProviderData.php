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

namespace Stormpath\Resource;

use Stormpath\Stormpath;

/** @since 1.13.0 */
class SamlProviderData extends InstanceResource
{
    const ENTITY_ID                                 = 'entityId';
    const X509_SIGNING_CERT                         = 'x509SigningCert';
    const ASSERTION_CONSUMER_SERVICE_POST_ENDPOINT  = 'assertionConsumerServicePostEndpoint';

    /**
     * This returns the value of the Entity Id from the IdP XML
     *
     * @since 1.13.0
     * @return string
     */
    public function getEntityId()
    {
        return $this->getProperty(self::ENTITY_ID);
    }

    /**
     * The certificate that is used to sign the requests sent to the IdP. If
     * you retrieve XML, the certificate will be embedded. If you retrieve
     * JSON, you’ll have to follow a further /x509certificates link to
     * retrieve it.
     *
     * @since 1.13.0
     * @param array $options
     * @return null|\Stormpath\Resource\X509SigningCert
     */
    public function getX509SigningCert(array $options = [])
    {
        return $this->getResourceProperty(self::X509_SIGNING_CERT, Stormpath::X509_SIGNING_CERT, $options);
    }

    /**
     * This is the location the IdP will send its response to.
     *
     * @since 1.13.0
     * @param array $options
     * @return null|\Stormpath\Resource\AssertionConsumerServicePostEndpoint
     */
    public function getAssertionConsumerServicePostEndpoint(array $options = [])
    {
        return $this->getResourceProperty(self::ASSERTION_CONSUMER_SERVICE_POST_ENDPOINT, Stormpath::ASSERTION_CONSUMER_SERVICE_POST_ENDPOINT);
    }
}