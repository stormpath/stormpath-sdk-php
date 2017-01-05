<?php
/**
 * Copyright 2017 Stormpath, Inc.
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

namespace Stormpath\Mfa;

use Stormpath\Client;
use Stormpath\DataStore\InternalDataStore;
use Stormpath\Resource\Resource;
use Stormpath\Stormpath;

class GoogleAuthenticatorFactor extends Factor
{
    const ISSUER = 'issuer';
    const SECRET = 'secret';
    const OTP_KEY_URI = 'keyUri';
    const ACCOUNT_NAME = 'accountName';
    const BASE_64_QR_IMAGE = 'base64QRImage';

    public function __construct(InternalDataStore $dataStore = null, \stdClass $properties = null, array $options = array())
    {
        parent::__construct($dataStore, $properties, $options);

        $this->setType('google-authenticator');
    }

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::GOOGLE_AUTHENTICATOR_FACTOR, self::PATH, $options);
    }

    public static function instantiate($properties = [])
    {
        $properties = array_merge($properties, ['type' => 'google_authenticator']);

        return Client::instantiate(Stormpath::GOOGLE_AUTHENTICATOR_FACTOR, $properties);
    }

    /**
     * Gets the issuer property.
     *
     * @return string
     */
    public function getIssuer()
    {
        return $this->getProperty(self::ISSUER);
    }

    /**
     * Sets the issuer property.
     *
     * @param string $issuer The issuer of the object
     *
     * @return self
     */
    public function setIssuer($issuer)
    {
        $this->setProperty(self::ISSUER, $issuer);

        return $this;
    }

    /**
     * Gets the secret property.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getProperty(self::SECRET);
    }

    /**
     * Gets the keyUri property.
     *
     * @return string
     */
    public function getKeyUri()
    {
        return $this->getProperty(self::OTP_KEY_URI);
    }

    /**
     * Gets the accountName property.
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->getProperty(self::ACCOUNT_NAME);
    }

    /**
     * Sets the accountName property.
     *
     * @param string $accountName The accountName of the object
     *
     * @return self
     */
    public function setAccountName($accountName)
    {
        $this->setProperty(self::ACCOUNT_NAME, $accountName);

        return $this;
    }

    /**
     * Gets the base64QRImage property.
     *
     * @return string
     */
    public function getBase64QRImage()
    {
        return $this->getProperty(self::BASE_64_QR_IMAGE);
    }

    /**
     * Gets the mostRecentChallenge resource property.
     *
     * @param array $options array of options
     *
     * @return Challenge
     */
    public function getMostRecentChallenge(array $options = [])
    {
        return $this->getResourceProperty(self::MOST_RECENT_CHALLENGE, Stormpath::GOOGLE_AUTHENTICATOR_CHALLENGE, $options);
    }

    public function validate($code)
    {
        $googleAuthenticatorChallenge = new GoogleAuthenticatorChallenge();
        $googleAuthenticatorChallenge->setCode($code);

        $returnedChallenge = $this->getDataStore()->create($this->href.'/challenges', $googleAuthenticatorChallenge, Stormpath::GOOGLE_AUTHENTICATOR_CHALLENGE);

        if ($returnedChallenge->getStatus() == Stormpath::SUCCESS) {
            return $returnedChallenge;
        }

        return false;
    }
}
