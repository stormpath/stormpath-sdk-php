<?php

/*
 * Copyright 2013 Stormpath, Inc.
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

namespace Stormpath\Tests;


class BaseTest extends \PHPUnit_Framework_TestCase
{
    const STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION = 'STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION';
    const STORMPATH_SDK_TEST_API_KEY_ID = 'STORMPATH_SDK_TEST_API_KEY_ID';
    const STORMPATH_SDK_TEST_API_KEY_SECRET = 'STORMPATH_SDK_TEST_API_KEY_SECRET';
    const BASE_URL = 'STORMPATH_BASE_URL';
    protected static $client;

    public static function setUpBeforeClass()
    {
        if (self::$client)
        {
            return;
        }

        if (array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION, $_SERVER) or array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION, $_ENV))
        {
            $apiKeyFileLocation = array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION, $_SERVER) ?
                                    $_SERVER[self::STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION] :
                                    $_ENV[self::STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION];

        } elseif ((array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_ID, $_SERVER) or array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_ID, $_ENV))
                    and (array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_SECRET, $_SERVER) or array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_SECRET, $_ENV)))
        {
            $apiKeyId = array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_ID, $_SERVER) ?
                        $_SERVER[self::STORMPATH_SDK_TEST_API_KEY_ID] :
                        $_ENV[self::STORMPATH_SDK_TEST_API_KEY_ID];

            $apiKeySecret = array_key_exists(self::STORMPATH_SDK_TEST_API_KEY_SECRET, $_SERVER) ?
                            $_SERVER[self::STORMPATH_SDK_TEST_API_KEY_SECRET] :
                            $_ENV[self::STORMPATH_SDK_TEST_API_KEY_SECRET];

            $apiKeyProperties = "apiKey.id=$apiKeyId\napiKey.secret=$apiKeySecret";
        }
        else
        {
            $message = "The '" . self::STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION . "' environment variable needs to be set before running the tests.\n" .
            "Alternatively, you can set the '" .self::STORMPATH_SDK_TEST_API_KEY_ID . "' and '" .self::STORMPATH_SDK_TEST_API_KEY_SECRET . "' environment " .
            "variables to make the tests run.";
            throw new \InvalidArgumentException($message);
        }

        $baseUrl = '';
        if (array_key_exists(self::BASE_URL, $_SERVER) or array_key_exists(self::BASE_URL, $_ENV))
        {
            $baseUrl = $_SERVER[self::BASE_URL] ?: $_ENV[self::BASE_URL];
        }

        \Stormpath\Client::$apiKeyFileLocation = $apiKeyFileLocation;
        \Stormpath\Client::$apiKeyProperties = $apiKeyProperties;
        \Stormpath\Client::$baseUrl = $baseUrl;
    }

    public function testClient()
    {
        self::$client = \Stormpath\Client::getInstance();

        $this->assertInstanceOf('Stormpath\Client', self::$client);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoApiKeyFile()
    {
        $builder = new \Stormpath\ClientBuilder();
        $builder->build();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidApiKeyId()
    {
        $builder = new \Stormpath\ClientBuilder();
        $builder->
            setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
            setApiKeyIdPropertyName('badId')->
            build();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidApiKeySecret()
    {
        $builder = new \Stormpath\ClientBuilder();
        $builder->
            setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
            setApiKeySecretPropertyName('badSecret')->
            build();
    }

    public function testClientFromPropertiesString()
    {
        $builder = new \Stormpath\ClientBuilder();
        $result = $builder->
                    setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
                    setApiKeyProperties("apiKey.id=something\napiKey.secret=somethingSecret")->
                    build();
        $this->assertInstanceOf('Stormpath\Client', $result);
    }

    protected static function createResource($parentHref, \Stormpath\Resource\Resource $resource, array $options = array())
    {
        if (!(strpos($parentHref, '/') === 0))
        {
            $parentHref = '/' . $parentHref;
        }

        $resource = self::$client->dataStore->create($parentHref, $resource, get_class($resource), $options);
        return $resource;
    }

}