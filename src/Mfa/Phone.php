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
 *
 */

namespace Stormpath\Mfa;

use Stormpath\Client;
use Stormpath\Resource\Deletable;
use Stormpath\Resource\Resource;
use Stormpath\Stormpath;

class Phone extends Resource implements Deletable
{
    const PATH                  = "phones";

    const CREATED_AT            = "createdAt";
    const MODIFIED_AT           = "modifiedAt";
    const STATUS                = "status";
    const VERIFICATION_STATUS   = "verificationStatus";
    const ACCOUNT               = "account";
    const PHONE_NUMBER          = "number";
    const DESCRIPTION           = "description";
    const NAME                  = "name";

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::PHONE, $properties);
    }

    /**
     * Gets the name property.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getProperty(self::NAME);
    }
    /**
     * Sets the name property.
     *
     * @param string $name The name of the object.
     * @return self
     */
    public function setName($name)
    {
        $this->setProperty(self::NAME, $name);

        return $this;
    }



    /**
     * Gets the description property.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getProperty(self::DESCRIPTION);
    }

    /**
     * Sets the description property.
     *
     * @param string $description The description of the object.
     * @return self
     */
    public function setDescription($description)
    {
        $this->setProperty(self::DESCRIPTION, $description);

        return $this;
    }



    /**
     * Gets the number property.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->getProperty(self::PHONE_NUMBER);
    }
    
    /**
     * Sets the number property.
     *
     * @param string|int $number The number of the object.
     * @return self
     */
    public function setNumber($number)
    {
        $this->setProperty(self::PHONE_NUMBER, $number);
        
        return $this; 
    } 

    /**
     * Gets the number property.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->getNumber();
    }

    /**
     * Sets the number property.
     *
     * @param string|int $phoneNumber The phoneNumber of the object.
     * @return self
     */
    public function setPhoneNumber($phoneNumber)
    {
        return $this->setNumber($phoneNumber);
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
     * Gets the status property.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getProperty(self::STATUS);
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
     * Gets the createdAt property.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getProperty(self::CREATED_AT);
    }

    /**
     * Delete this instance of phone from Stormpath.
     *
     * @return void
     */
    public function delete()
    {
        $this->getDataStore()->delete($this);
    }
}