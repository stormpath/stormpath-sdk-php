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


use Stormpath\Authc\Api\ApiKeyEncryptionUtils;
use Stormpath\Stormpath;

class ApiKey extends InstanceResource implements Deletable
{
    const ID        = "id";
    const SECRET    = "secret";
    const STATUS    = "status";

    const ACCOUNT   = "account";
    const TENANT    = "tenant";

    const PATH      = "apiKeys";

    private $apiKeyMetadata;

    public function getId()
    {
        return $this->getProperty(self::ID);
    }

    public function getSecret()
    {
        if ($this->apiKeyMetadata && $this->apiKeyMetadata->isEncryptSecret())
        {
            $secret = $this->getProperty(self::SECRET);
            $password = $this->getDataStore()->getApiKey()->getSecret();
            return ApiKeyEncryptionUtils::decrypt($secret, $password, $this->apiKeyMetadata);
        }
        else
        {
            return $this->getProperty(self::SECRET);
        }
    }

    public function getStatus()
    {
        return $this->getProperty(self::STATUS);
    }

    public function setStatus($status)
    {
        $this->setProperty(self::STATUS, $status);
    }

    public function getAccount($options = array())
    {
        return $this->getResourceProperty(self::ACCOUNT, Stormpath::ACCOUNT, $options);
    }

    public function getTenant($options = array())
    {
        return $this->getResourceProperty(self::TENANT, Stormpath::TENANT, $options);
    }

    public function setApiKeyMetadata($metadata)
    {
        $this->apiKeyMetadata = $metadata;
    }

    public function delete()
    {
        $this->getDataStore()->delete($this);
    }
}