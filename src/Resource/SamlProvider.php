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

use Stormpath\Client;
use Stormpath\DataStore\InternalDataStore;
use Stormpath\Saml\AttributeStatementMappingRules;
use Stormpath\Stormpath;

/** @since 1.13.0 */
class SamlProvider extends Provider implements Saveable
{
    const SSO_LOGIN_URL                         = "ssoLoginUrl";
    const SSO_LOGOUT_URL                        = "ssoLogoutUrl";
    const ENCODED_X509_SIGNING_CERT             = "encodedX509SigningCert";
    const REQUEST_SIGNATURE_ALGOITHM            = "requestSignatureAlgorithm";
    const ATTRIBUTE_STATEMENT_MAPPING_RULES     = "attributeStatementMappingRules";
    const SAML_PROVIDER_METADATA                = "serviceProviderMetadata";

    const SAML_PROVIDER_ID                      = "saml";

    public function __construct(InternalDataStore $dataStore = null, \stdClass $properties = null)
    {
        parent::__construct($dataStore, $properties);
        $this->setProperty(self::PROVIDER_ID, self::SAML_PROVIDER_ID);
    }

    /**
     * Retreive a SAML Provider based on the Href.
     *
     * @since 1.13.0
     * @param string $href
     * @param array $options
     * @return \Stormpath\Resource\SamlProvider
     */
    public static function get($href, array $options = [])
    {
        if (substr($href, 0 - strlen(self::PATH)) != self::PATH)
        {
            $href = $href.'/'.self::PATH;
        }

        return Client::get($href, Stormpath::SAML_PROVIDER, null, $options);
    }

    /**
     * Create a new SAML Provider instance with properties.
     *
     * @since 1.13.0
     * @param array|null $properties
     * @return \Stormpath\Resource\SamlProvider
     */
    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::SAML_PROVIDER, $properties);
    }

    /**
     * The URL at the IdP to which SAML authentication requests should be sent.
     * This is often called an “SSO URL”, “Login URL” or “Sign-in URL”
     *
     * @since 1.13.0
     * @return string
     */
    public function getSsoLoginUrl()
    {
        return $this->getProperty(self::SSO_LOGIN_URL);
    }

    /**
     *  The URL at the IdP to which SAML logout requests should be sent.
     * This is often called a “Logout URL”, “Global Logout URL” or “Single Logout URL”
     *
     * @since 1.13.0
     * @return string
     */
    public function getSsoLogoutUrl()
    {
        return $this->getProperty(self::SSO_LOGOUT_URL);
    }

    /**
     * The IdP will digitally sign auth assertions and Stormpath will need to
     * validate the signature. This will usually be in .pem or .crt format,
     * but Stormpath requires the text value.
     *
     * @since 1.13.0
     * @return string
     */
    public function getEncodedX509SigningCert()
    {
        return $this->getProperty(self::ENCODED_X509_SIGNING_CERT);
    }

    /**
     * You will need the name of the signing algorithm that your IdP uses.
     * It will be either “RSA-SHA256” or “RSA-SHA1”.
     *
     * @since 1.13.0
     * @return string
     */
    public function getRequestSignatureAlgorithm()
    {
        return $this->getProperty(self::REQUEST_SIGNATURE_ALGOITHM);
    }

    /**
     * Get the Metadata for the SAML Provider.
     *
     * @since 1.13.0
     * @param array $options
     * @return \Stormpath\Resource\SamlProviderData
     */
    public function getServiceProviderMetadata(array $options = [])
    {
        return $this->getResourceProperty(self::SAML_PROVIDER_METADATA, Stormpath::SAML_PROVIDER_DATA, $options);
    }

    /**
     * Get the Attribute Statement Mapping Rules for the SAML Provider.
     *
     * @since 1.13.0
     * @param array $options
     * @return \Stormpath\Saml\AttributeStatementMappingRules
     */
    public function getAttributeStatementMappingRules(array $options = [])
    {
        return $this->getResourceProperty(self::ATTRIBUTE_STATEMENT_MAPPING_RULES, Stormpath::ATTRIBUTE_STATEMENT_MAPPING_RULES, $options);
    }


    /**
     * Set the URL at the IdP to which SAML authentication requests should be sent.
     * This is often called an “SSO URL”, “Login URL” or “Sign-in URL”
     *
     * @since 1.13.0
     * @param $ssoLoginUrl
     * @return self
     */
    public function setSsoLoginUrl($ssoLoginUrl)
    {
        $this->setProperty(self::SSO_LOGIN_URL, $ssoLoginUrl);
        return $this;
    }

    /**
     * Set the URL at the IdP to which SAML logout requests should be sent.
     * This is often called a “Logout URL”, “Global Logout URL” or “Single Logout URL”
     *
     * @since 1.13.0
     * @param $ssoLogoutUrl
     * @return self
     */
    public function setSsoLogoutUrl($ssoLogoutUrl)
    {
        $this->setProperty(self::SSO_LOGOUT_URL, $ssoLogoutUrl);
        return $this;
    }

    /**
     * The IdP will digitally sign auth assertions and Stormpath will need to
     * validate the signature. This will usually be in .pem or .crt format,
     * but Stormpath requires the text value.
     *
     * @since 1.13.0
     * @param $encodedX509SigningCert
     * @return self
     */
    public function setEncodedX509SigningCert($encodedX509SigningCert)
    {
        $this->setProperty(self::ENCODED_X509_SIGNING_CERT, $encodedX509SigningCert);
        return $this;
    }

    /**
     * Set the name of the signing algorithm that your IdP uses.
     * It will be either “RSA-SHA256” or “RSA-SHA1”.
     *
     * @since 1.13.0
     * @param $requestSignatureAlgorithm
     * @return self
     */
    public function setRequestSignatureAlgorithm($requestSignatureAlgorithm)
    {
        $this->setProperty(self::REQUEST_SIGNATURE_ALGOITHM, $requestSignatureAlgorithm);
        return $this;
    }

    /**
     * Set the Attribute Statement Mapping Rules for the SAML Provider.
     *
     * @since 1.13.0
     * @param AttributeStatementMappingRules $attributeStatementMappingRules
     * @return self
     */
    public function setAttributeStatementMappingRules(AttributeStatementMappingRules $attributeStatementMappingRules)
    {
        $this->setProperty(self::ATTRIBUTE_STATEMENT_MAPPING_RULES, $attributeStatementMappingRules);
        return $this;
    }

    public function save()
    {
        $this->getDataStore()->save($this);
    }
}