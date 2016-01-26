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

namespace Stormpath\Tests;


use Stormpath\Client;

class TestCase extends \PHPUnit_Framework_TestCase
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

        $apiKeyProperties = null;
        $apiKeyFileLocation = null;
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
        \Stormpath\Client::$cacheManager = 'Array';

        self::$client = \Stormpath\Client::getInstance();

    }

    /**
     * This function instantiates a new client but doesn't modify
     * the TestCase::$client variable.
     */
    protected function newClientInstance()
    {
        $builder = new \Stormpath\ClientBuilder();

        $newClient = $builder->setApiKeyFileLocation(\Stormpath\Client::$apiKeyFileLocation)->
            setApiKeyProperties(\Stormpath\Client::$apiKeyProperties)->
            setApiKeyIdPropertyName(\Stormpath\Client::$apiKeyIdPropertyName)->
            setApiKeySecretPropertyName(\Stormpath\Client::$apiKeySecretPropertyName)->
            setCacheManager(\Stormpath\Client::$cacheManager)->
            setCacheManagerOptions(\Stormpath\Client::$cacheManagerOptions)->
            setBaseURL(\Stormpath\Client::$baseUrl)->
            build();

        return $newClient;
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


    public static function tearDownAfterClass()
    {
        self::$client = null;
        Client::tearDown();
    }

    protected static function getDummyCertForSaml()
    {
        //THIS IS A DUMMY CERT GENERATED AT https://samltool.com/self_signed_certs.php
        return "-----BEGIN CERTIFICATE-----
MIIDWTCCAkCgAwIBAgIBADANBgkqhkiG9w0BAQ0FADBGMQswCQYDVQQGEwJ1czEL
MAkGA1UECAwCQ0ExEjAQBgNVBAoMCVN0b3JtcGF0aDEWMBQGA1UEAwwNc3Rvcm1w
YXRoLmNvbTAeFw0xNjAxMTIxOTA5MTFaFw0xNzAxMTExOTA5MTFaMEYxCzAJBgNV
BAYTAnVzMQswCQYDVQQIDAJDQTESMBAGA1UECgwJU3Rvcm1wYXRoMRYwFAYDVQQD
DA1zdG9ybXBhdGguY29tMIIBIzANBgkqhkiG9w0BAQEFAAOCARAAMIIBCwKCAQIA
1/ldduqgCZ7PeBtZkfSOnbl69bJEpqzGBo9TPgEV8zXfoeEBGXAyYMqoic9o68Ud
upfRAwk7+XX+aHMh5bxhO7ie5h6wNe1RgCDlXJWkkfnEoMHKZ8i1abnkvTa6Bi0o
NqbbC+luuOx0gKlpAl2eBR2lFDH/ACGIM9jSfwVBLjbzaSfZIfZ8DherxsNrC03t
r9bM+tmUtxveZ55npPI1fB4fjmLNPgYSv9QhOQOuEMUC6QnX30KN/R/aiTIPeVJH
a4FKH4Ad6oZbjcQxzckR0NDgDgy3B6+pm5IgcnICoRIjd0jK5h83eXwXbvb6wUEz
gHEKFJ0+z4xIoXXasnzLnNMCAwEAAaNQME4wHQYDVR0OBBYEFIAzgHtK1XgsRiG3
W+GaZwwBqsEZMB8GA1UdIwQYMBaAFIAzgHtK1XgsRiG3W+GaZwwBqsEZMAwGA1Ud
EwQFMAMBAf8wDQYJKoZIhvcNAQENBQADggECAGwRBabrldcTSoSm03/pXBvosS/E
RM82A0kfIYZSeStMU0LBSacSNooBtJNY7o0ZATGObWjHQmj2u8e6Qgt8PHeOvYCw
L1pTnNygLpKmrgWdGMgUM+yMLmduvaBXRFLrb9xhwoiOB3b2CZtqyvOgmudRN3M5
FSGg2SOHbdpEqN8sWY+LjNLmVPsGxCbI4OHeLXfZ0fCiJVCZi6ep+STFgQHsuMF6
exOFNg/LNzrC8e4ldJ/U0hjmqDctvFYqSjWVfqu8GzOrEsdteDapVJxHu6dD9TAu
HeqcSVN05izamtRPc1BUeLos/6LuUpDztlolcXaD+ISTv2/G13L3dxfvub7s
-----END CERTIFICATE-----";
    }

}