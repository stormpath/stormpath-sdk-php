<?php

class Services_Stormpath_Client_Client
{
    private $dataStore;

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
