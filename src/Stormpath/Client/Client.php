<?php

namespace Stormpath\Client;

use Stormpath\Client\ApiKey;
use Stormpath\Http\HttpClientRequestExecutor;
use Stormpath\DataStore\DefaultDataStore;
use Stormpath\Service\StormpathService;

class Client
{
    private $dataStore;

    public function __construct(ApiKey $apiKey, $baseUrl = null)
    {
        $requestExecutor = new HttpClientRequestExecutor($apiKey);
        $this->dataStore = new DefaultDataStore($requestExecutor, $baseUrl);
    }

    public function getCurrentTenant()
    {
        return $this->dataStore->getResource('/tenants/current', StormpathService::TENANT);
    }

    public function getDataStore()
    {
        return $this->dataStore;
    }

}
