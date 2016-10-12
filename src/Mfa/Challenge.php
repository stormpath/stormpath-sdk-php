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
use Stormpath\Resource\Account;
use Stormpath\Resource\Deletable;
use Stormpath\Resource\InstanceResource;
use Stormpath\Stormpath;

abstract class Challenge extends InstanceResource implements Deletable
{
    const CODE          = "code";
    const FACTOR        = "factor";
    const STATUS        = "status";
    const ACCOUNT       = "account";
    const MESSAGE       = "message";
    const CREATED_AT    = "createdAt";
    const MODIFIED_AT   = "modifiedAt";

    const PATH          = "challenges";

    public abstract static function instantiate($properties = null);

    public abstract function validate($code);

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
     * Gets the factor resource property.
     *
     * @param array $options array of options.
     * @return Factor
     */
    public function getFactor(array $options = [])
    {
        return $this->getResourceProperty(self::FACTOR, Stormpath::FACTOR, $options);
    }

    /**
     * Sets the factor resource property.
     *
     * @param Factor $factor The factor of the object.
     * @return self
     */
    public function setFactor(Factor $factor)
    {
        $this->setResourceProperty(self::FACTOR, $factor);

        return $this;
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
     * Sets the account resource property.
     *
     * @param Account $account The account of the object.
     * @return self
     */
    public function setAccount(Account $account)
    {
        $this->setResourceProperty(self::ACCOUNT, $account);

        return $this;
    }

    /**
     * Sets the code property.
     *
     * @param string $code The code of the object.
     * @return self
     */
    public function setCode($code)
    {
        $this->setProperty(self::CODE, $code);

        return $this;
    }


    /**
     * Delete the challenge.
     *
     * @return string
     */
    public function delete()
    {
        return $this->getDataStore()->delete($this);
    }

}