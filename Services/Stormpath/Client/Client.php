<?php

/*
 * Copyright 2012 Stormpath, Inc.
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

/**
 * The {@code Services_Stormpath_Client_Client} is the main entry point to the Stormpath PHP SDK.  A PHP project wishing to
 * communicate with the Stormpath REST API service must instantiate a {@code Services_Stormpath_Client_Client} instance.
 * After obtaining a {@code Services_Stormpath_Client_Client instance}, the REST API may be used by making simple PHP calls
 * on objects returned from the Client (or any children objects obtained therein).
 * <p/>
 * For example:
 * <pre>
 * $accessId = //<a href="http://www.stormpath.com/docs/quickstart/connect">Your Stormpath API Key's Access ID</a>
 * $secret = //<a href="http://www.stormpath.com/docs/quickstart/connect">Your Stormpath API Key's Secret</a>
 *
 * //create the Client instance:
 * $client = new Services_Stormpath_Client_Client(new Services_Stormpath_Client_ApiKey($accessId, $secret));
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
 * @see <a href="http://www.stormpath.com/docs/quickstart/connect">Communicating with Stormpath: Get your API Key</a>
 */
class Services_Stormpath_Client_Client
{
    private $dataStore;

    /**
     * Instantiates a new Services_Stormpath_Client_Client instance that will communicate with the Stormpath REST API.
     * See the class-level PHPDoc for a usage example.
     *
     * @param $apiKey the Stormpath account API Key that will be used to authenticate the client with
     *               Stormpath's REST API.
     *
     * @param $baseUrl optional parameter for specifying the base URL when not using the default one
     *         (https://api.stormpath.com/v1).
     */
    public function __construct(Services_Stormpath_Client_ApiKey $apiKey, $baseUrl = null)
    {
        $requestExecutor = new Services_Stormpath_Http_HttpClientRequestExecutor($apiKey);
        $this->dataStore = new Services_Stormpath_DataStore_DefaultDataStore($requestExecutor, $baseUrl);
    }

    public function getCurrentTenant()
    {
        return $this->dataStore->getResource('/tenants/current', Services_Stormpath::TENANT);
    }

    public function getDataStore()
    {
        return $this->dataStore;
    }

}
