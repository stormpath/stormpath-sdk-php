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

use Stormpath\Authc\Api\ApiKeyEncryptionOptions;
use Stormpath\Client;
use Stormpath\Mfa\Factor;
use Stormpath\Mfa\FactorList;
use Stormpath\Mfa\Phone;
use Stormpath\Mfa\PhoneList;
use Stormpath\Stormpath;

class Account extends InstanceResource implements Deletable
{
    const USERNAME                 = "username";
    const EMAIL                    = "email";
    const FACTORS                  = "factors";
    const PASSWORD                 = "password";
    const GIVEN_NAME               = "givenName";
    const MIDDLE_NAME              = "middleName";
    const SURNAME                  = "surname";
    const STATUS                   = "status";
    const GROUPS                   = "groups";
    const CUSTOM_DATA              = "customData";
    const DIRECTORY                = "directory";
    const EMAIL_VERIFICATION_TOKEN = "emailVerificationToken";
    const GROUP_MEMBERSHIPS        = "groupMemberships";
    const FULL_NAME                = "fullName";
    const TENANT                   = "tenant";
    const PROVIDER_DATA			   = "providerData";
    const PHONES                   = "phones";
    const ACCESS_TOKENS            = "accessTokens";
    const REFRESH_TOKENS           = "refreshTokens";
    const PASSWORD_MODIFIED_AT     = "passwordModifiedAt";

    const PATH                     = "accounts";

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::ACCOUNT, self::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::ACCOUNT, $properties);
    }

    public function getUsername()
    {
        return $this->getProperty(self::USERNAME);
    }

    public function setUsername($username)
    {
        $this->setProperty(self::USERNAME, $username);
    }

    public function getEmail()
    {
        return $this->getProperty(self::EMAIL);
    }

    public function setEmail($email)
    {
        $this->setProperty(self::EMAIL, $email);
    }

    public function setPassword($password)
    {
        $this->setProperty(self::PASSWORD, $password);
    }

    public function getGivenName()
    {
        return $this->getProperty(self::GIVEN_NAME);
    }

    public function setGivenName($givenName)
    {
        $this->setProperty(self::GIVEN_NAME, $givenName);
    }

    public function getMiddleName()
    {
        return $this->getProperty(self::MIDDLE_NAME);
    }

    public function setMiddleName($middleName)
    {
        return $this->setProperty(self::MIDDLE_NAME, $middleName);
    }

    public function getSurname()
    {
        return $this->getProperty(self::SURNAME);
    }

    public function setSurname($surname)
    {
        $this->setProperty(self::SURNAME, $surname);
    }

    public function getStatus()
    {
        $value = $this->getProperty(self::STATUS);

        if ($value)
        {
            $value = strtoupper($value);
        }

        return $value;
    }

    public function setStatus($status)
    {
        $uprStatus = strtoupper($status);
        if (array_key_exists($uprStatus, Stormpath::$AccountStatuses))
        {
            $this->setProperty(self::STATUS, Stormpath::$AccountStatuses[$uprStatus]);
        }
    }


    public function getFullName() {

        return $this->getProperty(self::FULL_NAME);
    }

    public function getGroups(array $options = array())
    {
        return $this->getResourceProperty(self::GROUPS, Stormpath::GROUP_LIST, $options);
    }

    public function getCustomData(array $options = array())
    {
        $customData =  $this->getResourceProperty(self::CUSTOM_DATA, Stormpath::CUSTOM_DATA, $options);

        if(!$customData) {
            $customData = new CustomData();
            $this->setProperty(self::CUSTOM_DATA, $customData);
        }

        return $customData;
    }

    public function getDirectory(array $options = array())
    {
        return $this->getResourceProperty(self::DIRECTORY, Stormpath::DIRECTORY, $options);
    }

    public function getAccessTokens(array $options = array())
    {
        return $this->getResourceProperty(self::ACCESS_TOKENS, Stormpath::ACCESS_TOKEN_LIST, $options);
    }

    public function getRefreshTokens(array $options = array())
    {
        return $this->getResourceProperty(self::REFRESH_TOKENS, Stormpath::REFRESH_TOKEN_LIST, $options);
    }

    public function getEmailVerificationToken(array $options = array())
    {
        return $this->getResourceProperty(self::EMAIL_VERIFICATION_TOKEN, Stormpath::EMAIL_VERIFICATION_TOKEN, $options);
    }

    public function getGroupMemberships(array $options = array())
    {
        return $this->getResourceProperty(self::GROUP_MEMBERSHIPS, Stormpath::GROUP_MEMBERSHIP_LIST, $options);
    }

    public function getTenant(array $options = array()) {

        return $this->getResourceProperty(self::TENANT, Stormpath::TENANT, $options);
    }

    /**
     * Gets the phones resource property.
     *
     * @param array $options array of options.
     * @return PhoneList
     */
    public function getPhones(array $options = [])
    {
        return $this->getResourceProperty(self::PHONES, Stormpath::PHONE_LIST, $options);
    }

    /**
     * @param Phone $phone
     * @param array $options
     */
    public function addPhone(Phone $phone, $options = [])
    {
        return $this->getDataStore()->create($this->getHref() . '/' . $phone::PATH,
            $phone, Stormpath::PHONE, $options);
    }

    /**
     * Gets the factors resource property.
     *
     * @param array $options array of options.
     * @return FactorList
     */
    public function getFactors(array $options = [])
    {
        return $this->getResourceProperty(self::FACTORS, Stormpath::FACTOR_LIST, $options);
    }

    public function addFactor(Factor $factor, $options = [])
    {
        $href = $this->getHref() . '/' . $factor::PATH;

        if(null !== $factor->getChallenge()) {
            $href .= '?challenge=true';
        }

        return $this->getDataStore()->create($href,
            $factor, get_class($factor), $options);
    }


    public function getPasswordModifedAt()
    {
        return $this->getProperty(self::PASSWORD_MODIFIED_AT );
    }
    
    public function getProviderData(array $options = array())
    {
    	$value = $this->getProperty(self::PROVIDER_DATA);
    	
    	if ($value instanceof ProviderData)
    	{
    		return $value;
    	}
    	
    	if ($value instanceof \stdClass)
    	{
    		$href = $value->href;
    		
    		if (empty($href))
    		{
    			throw new \InvalidArgumentException("providerData resource does not contain its required href property.");    			
    		}
    		
    		$providerData = $this->getDataStore()->getResource($href, Stormpath::PROVIDER_DATA, array(
                'propertyId' => 'providerId'
            ));
    		$this->setProperty(self::PROVIDER_DATA, $providerData);
    		
    		return $providerData;
    	}
    	
    	throw new \InvalidArgumentException("providerData does not match expected type ProviderData or stdClass");
    }

    public function addGroup(Group $group, array $options = array())
    {
        return GroupMembership::_create($this, $group, $this->getDataStore(), $options);
    }

    public function delete() {

        $this->getDataStore()->delete($this);
    }

    public function createApiKey($options = array())
    {
        $apiKeyOptions = new ApiKeyEncryptionOptions($options);
        $options = array_merge($options, $apiKeyOptions->toArray());

        $properties = new \stdClass();

        if(isset($options['name'])) { $properties->name = $options['name']; }
        if(isset($options['description'])) { $properties->description = $options['description']; }

        $apiKey = $this->getDataStore()->instantiate(Stormpath::API_KEY, $properties);

        $apiKey = $this->getDataStore()->create($this->getHref() . '/' . ApiKey::PATH,
            $apiKey, Stormpath::API_KEY, $options);

        if ($apiKey)
        {
            $apiKey->setApiKeyMetadata($apiKeyOptions);
        }

        return $apiKey;
    }
}
