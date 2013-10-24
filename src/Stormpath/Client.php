<?php

namespace Stormpath;

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

use Stormpath\DataStore\DefaultDataStore;
use Stormpath\Http\HttpClientRequestExecutor;
use Stormpath\Resource\Resource;
use Stormpath\Stormpath;
use Stormpath\Util\Magic;

function toObject($properties)
{
    if (is_array($properties)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return (object) array_map(__FUNCTION__, $properties);
    }

    // if it's not an array, it's assumed to be an object
    return $properties;
}

/**
 * The {@code Client} is the main entry point to the Stormpath PHP SDK.  A PHP project wishing to
 * communicate with the Stormpath REST API service must instantiate a <i>Client</i> instance.
 * After obtaining a <i>Client</i> instance, the REST API may be used by making simple PHP calls
 * on objects returned from the Client (or any children objects obtained therein).
 * <p/>
 * For example:
 * <pre>
 * $accessId = //<a href="http://docs.stormpath.com/console/product-guide/#manage-api-keys">Your Stormpath API Key's Access ID</a>
 * $secret = //<a href="http://docs.stormpath.com/console/product-guide/#manage-api-keys">Your Stormpath API Key's Secret</a>
 *
 * //create the Client instance:
 * $client = new Client(new ApiKey($accessId, $secret));
 *
 * //interact with the REST API resources as desired:
 * $myTenant = $client->getCurrentTenant();
 *
 * $applications = tenant->getApplications();
 *
 * echo "My Applications: ";
 * foreach ($applications as $application) {
 *     echo $application->getName();
 * }
 * </pre>
 *
 * @since 0.1.0
 * @see <a href="http://docs.stormpath.com/rest/quickstart/#get-an-api-key">Communicating with Stormpath: Get your API Key</a>
 */
class Client extends Magic
{

    public static $apiKeyFileLocation;

    public static $apiKeyProperties;

    public static $apiKeyIdPropertyName = "apiKey.id";

    public static $apiKeySecretPropertyName = "apiKey.secret";

    public static $baseUrl;

    private static $instance;

    private $dataStore;

    /**
     * Instantiates a new Client instance that will communicate with the Stormpath REST API.
     * See the class-level PHPDoc for a usage example.
     *
     * @param $apiKey the Stormpath account API Key that will be used to authenticate the client with
     *               Stormpath's REST API.
     *
     * @param $baseUrl optional parameter for specifying the base URL when not using the default one
     *         (https://api.stormpath.com/v1).
     */
    public function __construct(ApiKey $apiKey, $baseUrl = null)
    {
        parent::__construct();
        $requestExecutor = new HttpClientRequestExecutor($apiKey);
        $this->dataStore = new DefaultDataStore($requestExecutor, $baseUrl);
    }

    public static function get($href, $className, $path = null, array $options = array())
    {

        $resultingHref = $href;
        if ($path and stripos($href, $path) === false)
        {
            $resultingHref = is_numeric(stripos($href, $path)) ? $href : "$path/$href";
        }

        return self::getInstance()->dataStore->getResource($resultingHref, $className, $options);
    }

    public static function instantiate($className, $properties = null)
    {
        return self::getInstance()->dataStore->instantiate($className, toObject($properties));
    }

    public static function create($parentHref, Resource $resource, array $options = array())
    {
        return self::getInstance()->dataStore->create($parentHref, $resource, get_class($resource), $options);
    }

    // @codeCoverageIgnoreStart
    public static function verifyEmailToken($token)
    {
        //TODO: enable auto discovery via Tenant resource (should be just /emailVerificationTokens)
        $href = "/accounts/emailVerificationTokens/" . $token;

        $tokenProperties = new \stdClass();
        $hrefName = Resource::HREF_PROP_NAME;
        $tokenProperties->$hrefName = $href;

        $evToken = self::getInstance()->dataStore->instantiate(Stormpath::EMAIL_VERIFICATION_TOKEN, $tokenProperties);

        return self::getInstance()->dataStore->save($evToken, Stormpath::ACCOUNT);
    }
    // @codeCoverageIgnoreEnd

    public static function getInstance()
    {
        if (!self::$instance)
        {
            $builder = new ClientBuilder();
            self::$instance = $builder->setApiKeyFileLocation(self::$apiKeyFileLocation)->
                              setApiKeyProperties(self::$apiKeyProperties)->
                              setApiKeyIdPropertyName(self::$apiKeyIdPropertyName)->
                              setApiKeySecretPropertyName(self::$apiKeySecretPropertyName)->
                              setBaseURL(self::$baseUrl)->
                              build();
        }

        return self::$instance;
    }

    public function getTenant(array $options = array())
    {
        return $this->getCurrentTenant($options);
    }

    public function getCurrentTenant(array $options = array())
    {
        return $this->dataStore->getResource('/tenants/current', Stormpath::TENANT, $options);
    }

    public function getDataStore()
    {
        return $this->dataStore;
    }

}
