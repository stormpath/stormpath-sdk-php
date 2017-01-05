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

namespace Stormpath\Resource;

use Stormpath\Client;
use Stormpath\DataStore\InternalDataStore;
use Stormpath\Stormpath;

class GithubProvider extends Provider
{
    const CLIENT_ID = 'clientId';
    const CLIENT_SECRET = 'clientSecret';
    const GITHUB_PROVIDER_ID = 'github';

    public static function get($href, array $options = array())
    {
        if (substr($href, 0 - strlen(self::PATH)) != self::PATH) {
            $href = $href.'/'.self::PATH;
        }

        return Client::get($href, Stormpath::GITHUB_PROVIDER, Directory::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::GITHUB_PROVIDER, $properties);
    }

    public function __construct(InternalDataStore $dataStore = null, \stdClass $properties = null)
    {
        parent::__construct($dataStore, $properties);
        $this->setProperty(self::PROVIDER_ID, self::GITHUB_PROVIDER_ID);
    }

    public function getClientId()
    {
        return $this->getProperty(self::CLIENT_ID);
    }

    public function setClientId($clientId)
    {
        $this->setProperty(self::CLIENT_ID, $clientId);
    }

    public function getClientSecret()
    {
        return $this->getProperty(self::CLIENT_SECRET);
    }

    public function setClientSecret($clientSecret)
    {
        $this->setProperty(self::CLIENT_SECRET, $clientSecret);
    }
}
