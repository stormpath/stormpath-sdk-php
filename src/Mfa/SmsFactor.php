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

use Stormpath\Client;
use Stormpath\DataStore\InternalDataStore;
use Stormpath\Resource\Resource;
use Stormpath\Stormpath;

class SmsFactor extends Factor
{
    const PHONE = 'phone';
    const CHALLENGE = 'challenge';


    public function __construct(InternalDataStore $dataStore = null, \stdClass $properties = null, array $options = array())
    {
        parent::__construct($dataStore, $properties, $options);

        $this->setType('sms');
    }

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::SMS_FACTOR, self::PATH, $options);
    }

    public static function instantiate($properties = [])
    {
        $properties = array_merge($properties, ['type'=>'sms']);
        return Client::instantiate(Stormpath::SMS_FACTOR, $properties);
    }


    /**
     * Gets the phone resource property.
     *
     * @param array $options array of options.
     * @return Phone
     */
    public function getPhone(array $options = [])
    {
        $phone = $this->getProperty(self::PHONE);
        return $this->getResourceProperty(self::PHONE, Stormpath::PHONE, $options);
    }

    /**
     * Sets the phone property.
     *
     * @param Phone|string $phone The phone of the object.
     * @return self
     */
    public function setPhone($phone)
    {
        if( ! $phone instanceof Phone )
        {

            $properties = new \stdClass();
            $properties->number = $phone;
            $phone = $this->getDataStore()->instantiate(
                Stormpath::PHONE,
                $properties
            );

            $this->setProperty(self::PHONE, $phone);
            return $this;
        }

        $this->setResourceProperty(self::PHONE, $phone);
        return $this;


    }
    
    /**
     * Gets the challenge property.
     *
     * @return Challenge
     */
    public function getChallenge()
    {
        return $this->getProperty(self::CHALLENGE);
    } 
    
    

    /**
     * Sets the challenge.
     *
     * @param Challenge|string $challenge The challenge object.
     * @return self
     */
    public function setChallenge($challenge)
    {
        if( ! $challenge instanceof SmsChallenge )
        {
            if(!strpos($challenge, Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER)) {
                throw new \InvalidArgumentException('The challenge message must have a challenge code placeholder: ' . Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER);
            }
            $properties = new \stdClass();
            $properties->message = $challenge;
            $challenge = $this->getDataStore()->instantiate(
                Stormpath::SMS_CHALLENGE,
                $properties
            );

            $this->setProperty(self::CHALLENGE, $challenge);
            return $this;
        }

        $this->setResourceProperty(self::CHALLENGE, $challenge);
        return $this;


    }


    /**
     * Gets the mostRecentChallenge resource property.
     *
     * @param array $options array of options.
     * @return Challenge
     */
    public function getMostRecentChallenge(array $options = [])
    {
        return $this->getResourceProperty(self::MOST_RECENT_CHALLENGE, Stormpath::SMS_CHALLENGE, $options);
    }

    public function createChallenge($message = null)
    {
        if(isset($message) &&
            !strpos($message, Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER)) {
            throw new \InvalidArgumentException('The challenge message must have a challenge code placeholder: ' . Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER);
        }
        $challenge = new SmsChallenge();

        if($message) {
            $challenge->message = $message;
        }


        return $this->getDataStore()->create($this->href . '/challenges', $challenge, Stormpath::SMS_CHALLENGE);
    }
}