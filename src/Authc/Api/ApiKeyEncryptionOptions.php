<?php

namespace Stormpath\Authc\Api;

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

class ApiKeyEncryptionOptions
{
    const ENCRYPT_SECRET = 'encryptSecret';
    const ENCRYPTION_KEY_SIZE = 'encryptionKeySize';
    const ENCRYPTION_KEY_ITERATIONS = 'encryptionKeyIterations';
    const ENCRYPTION_KEY_SALT = 'encryptionKeySalt';

    const DEFAULT_ENCRYPTION_KEY_SIZE = 256;
    const DEFAULT_ENCRYPTION_KEY_ITERATIONS = 1024;

    private $options;

    public function __construct(array $options = array())
    {
        $this->options = array();

        if (isset($options[self::ENCRYPT_SECRET]) and $options[self::ENCRYPT_SECRET]) {
            $this->options[self::ENCRYPT_SECRET] = $options[self::ENCRYPT_SECRET];

            $this->options[self::ENCRYPTION_KEY_SIZE] = isset($options[self::ENCRYPTION_KEY_SIZE]) ?
                $options[self::ENCRYPTION_KEY_SIZE] :
                self::DEFAULT_ENCRYPTION_KEY_SIZE;

            $this->options[self::ENCRYPTION_KEY_ITERATIONS] = isset($options[self::ENCRYPTION_KEY_ITERATIONS]) ?
                $options[self::ENCRYPTION_KEY_ITERATIONS] :
                self::DEFAULT_ENCRYPTION_KEY_ITERATIONS;

            $salt = openssl_random_pseudo_bytes(16);

            $this->options[self::ENCRYPTION_KEY_SALT] = ApiKeyEncryptionUtils::base64url_encode($salt);
        }
    }

    public function isEncryptSecret()
    {
        return isset($this->options[self::ENCRYPT_SECRET]) and $this->options[self::ENCRYPT_SECRET];
    }

    public function getEncryptionKeySize()
    {
        return $this->options[self::ENCRYPTION_KEY_SIZE];
    }

    public function getEncryptionKeyIterations()
    {
        return $this->options[self::ENCRYPTION_KEY_ITERATIONS];
    }

    public function getEncryptionKeySalt()
    {
        return $this->options[self::ENCRYPTION_KEY_SALT];
    }

    public function toArray()
    {
        return $this->options;
    }

}