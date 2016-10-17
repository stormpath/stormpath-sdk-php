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

use Stormpath\Resource\Account;
use Stormpath\Resource\Deletable;
use Stormpath\Resource\InstanceResource;
use Stormpath\Stormpath;

abstract class Factor extends InstanceResource implements Deletable
{
    const PATH  = 'factors';

    const TYPE  = 'type';
    const CREATED_AT = 'createdAt';
    const MODIFIED_AT   = 'modifiedAt';
    const STATUS        = 'status';
    const VERIFICATION_STATUS   = 'verificationStatus';
    const ACCOUNT               = 'account';
    const CHALLENGES            = 'challenges';
    const MOST_RECENT_CHALLENGE = "mostRecentChallenge";

    /**
     * Gets the type property.
     *
     * @return string
     */
    public function getType()
    {
        return $this->getProperty(self::TYPE);
    } 
    
    /**
     * Sets the type property.
     *
     * @param string $type The type of the object.
     * @return self
     */
    protected function setType($type)
    {
        $this->setProperty(self::TYPE, $type);
        
        return $this; 
    } 
    
    

    /**
     * Gets the createdAt property.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getProperty(self::CREATED_AT);
    }

    /**
     * Gets the modifiedAt property.
     *
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->getProperty(self::MODIFIED_AT);
    }

    /**
     * Gets the status property.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getProperty(self::STATUS);
    }

    /**
     * Sets the status property.
     *
     * @param string $status The status of the object.
     * @return self
     */
    public function setStatus($status)
    {
        $this->setProperty(self::STATUS, $status);

        return $this;
    }

    /**
     * Gets the verificationStatus property.
     *
     * @return string
     */
    public function getVerificationStatus()
    {
        return $this->getProperty(self::VERIFICATION_STATUS);
    }

    /**
     * Gets the account resource property.
     *
     * @param array $options array of options.
     * @return Account
     */
    public function getAccount(array $options = [])
    {
        return $this->getResourceProperty(self::ACCOUNT, Stormpath::ACCOUNT, $options);
    }

    /**
     * Gets the challenges resource property.
     *
     * @param array $options array of options.
     * @return ChallengeList
     */
    public function getChallenges(array $options = [])
    {
        return $this->getResourceProperty(self::CHALLENGES, Stormpath::CHALLENGES, $options);
    }

    /**
     * Gets the mostRecentChallenge resource property.
     *
     * @param array $options array of options.
     * @return Challenge
     */
    public abstract function getMostRecentChallenge(array $options = []);


    public function delete()
    {
        return $this->getDataStore()->delete($this);
    }



}
