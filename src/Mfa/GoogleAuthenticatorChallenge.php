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
use Stormpath\Stormpath;

class GoogleAuthenticatorChallenge extends Challenge
{


    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::GOOGLE_AUTHENTICATOR_CHALLENGE, self::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::GOOGLE_AUTHENTICATOR_CHALLENGE, $properties);
    }

    /**
     * Validate the current Challenge.
     *
     * @param string $code The code to validate the challenge with.
     * @return SmsChallenge|boolean
     */
    public function validate($code)
    {
        $this->setCode($code);

        $returnedChallenge = $this->getDataStore()->save($this, Stormpath::GOOGLE_AUTHENTICATOR_CHALLENGE);

        if($returnedChallenge->getStatus() == Stormpath::SUCCESS) {
            return $returnedChallenge;
        }

        return false;
    }


}