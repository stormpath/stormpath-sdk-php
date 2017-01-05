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

namespace Stormpath\Directory;

use Stormpath\Client;
use Stormpath\Resource\InstanceResource;
use Stormpath\Stormpath;

class PasswordStrength extends InstanceResource
{
    const HREF = 'href';
    const MIN_LENGTH = 'minLength';
    const MAX_LENGTH = 'maxLength';
    const MIN_NUMERIC = 'minNumeric';
    const MIN_SYMBOL = 'minSymbol';
    const MIN_DIACRITIC = 'minDiacritic';
    const PREVENT_REUSE = 'preventReuse';
    const MIN_LOWER_CASE = 'minLowerCase';
    const MIN_UPPER_CASE = 'minUpperCase';

    const PATH = 'strength';

    public static function get($href, array $options = [])
    {
        return Client::get($href, Stormpath::PASSWORD_STRENGTH, self::PATH, $options);
    }
    /**
     * Gets the href property.
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getProperty(self::HREF);
    }
    /**
     * Gets the minLength property.
     *
     * @return int
     */
    public function getMinLength()
    {
        return $this->getProperty(self::MIN_LENGTH);
    }

    /**
     * Sets the minLength property.
     *
     * @param int $minLength The minLength of the object
     *
     * @return self
     */
    public function setMinLength($minLength)
    {
        $this->setProperty(self::MIN_LENGTH, $minLength);

        return $this;
    }

    /**
     * Gets the maxLength property.
     *
     * @return int
     */
    public function getMaxLength()
    {
        return $this->getProperty(self::MAX_LENGTH);
    }

    /**
     * Sets the maxLength property.
     *
     * @param int $maxLength The maxLength of the object
     *
     * @return self
     */
    public function setMaxLength($maxLength)
    {
        $this->setProperty(self::MAX_LENGTH, $maxLength);

        return $this;
    }

    /**
     * Gets the minLowerCase property.
     *
     * @return int
     */
    public function getMinLowerCase()
    {
        return $this->getProperty(self::MIN_LOWER_CASE);
    }

    /**
     * Sets the minLowerCase property.
     *
     * @param int $minLowerCase The minLowerCase of the object
     *
     * @return self
     */
    public function setMinLowerCase($minLowerCase)
    {
        $this->setProperty(self::MIN_LOWER_CASE, $minLowerCase);

        return $this;
    }

    /**
     * Gets the minUpperCase property.
     *
     * @return int
     */
    public function getMinUpperCase()
    {
        return $this->getProperty(self::MIN_UPPER_CASE);
    }

    /**
     * Sets the minUpperCase property.
     *
     * @param int $minUpperCase The minUpperCase of the object
     *
     * @return self
     */
    public function setMinUpperCase($minUpperCase)
    {
        $this->setProperty(self::MIN_UPPER_CASE, $minUpperCase);

        return $this;
    }

    /**
     * Gets the minNumeric property.
     *
     * @return int
     */
    public function getMinNumeric()
    {
        return $this->getProperty(self::MIN_NUMERIC);
    }

    /**
     * Sets the minNumeric property.
     *
     * @param int $minNumeric The minNumeric of the object
     *
     * @return self
     */
    public function setMinNumeric($minNumeric)
    {
        $this->setProperty(self::MIN_NUMERIC, $minNumeric);

        return $this;
    }

    /**
     * Gets the minSymbol property.
     *
     * @return int
     */
    public function getMinSymbol()
    {
        return $this->getProperty(self::MIN_SYMBOL);
    }

    /**
     * Sets the minSymbol property.
     *
     * @param int $minSymbol The minSymbol of the object
     *
     * @return self
     */
    public function setMinSymbol($minSymbol)
    {
        $this->setProperty(self::MIN_SYMBOL, $minSymbol);

        return $this;
    }

    /**
     * Gets the minDiacritic property.
     *
     * @return int
     */
    public function getMinDiacritic()
    {
        return $this->getProperty(self::MIN_DIACRITIC);
    }

    /**
     * Sets the minDiacritic property.
     *
     * @param int $minDiacritic The minDiacritic of the object
     *
     * @return self
     */
    public function setMinDiacritic($minDiacritic)
    {
        $this->setProperty(self::MIN_DIACRITIC, $minDiacritic);

        return $this;
    }

    /**
     * Gets the preventReuse property.
     *
     * @return int
     */
    public function getPreventReuse()
    {
        return $this->getProperty(self::PREVENT_REUSE);
    }

    /**
     * Sets the preventReuse property.
     *
     * @param int $preventReuse The preventReuse of the object
     *
     * @return self
     */
    public function setPreventReuse($preventReuse)
    {
        $this->setProperty(self::PREVENT_REUSE, $preventReuse);

        return $this;
    }
}
