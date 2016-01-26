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
use Stormpath\Stormpath;

class Account extends InstanceResource implements Deletable
{
    const USERNAME                 = "username";
    const EMAIL                    = "email";
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
    const ACCESS_TOKENS            = "accessTokens";
    const REFRESH_TOKENS           = "refreshTokens";

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

        $apiKey = $this->getDataStore()->instantiate(Stormpath::API_KEY);

        $apiKey = $this->getDataStore()->create($this->getHref() . '/' . ApiKey::PATH,
            $apiKey, Stormpath::API_KEY, $options);

        if ($apiKey)
        {
            $apiKey->setApiKeyMetadata($apiKeyOptions);
        }

        return $apiKey;
    }
}
