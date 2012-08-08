<?php

class Services_Stormpath_Http_HttpClientRequestExecutor
    implements Services_Stormpath_Http_RequestExecutor
{
    private $apiKey;
    private $httpClient;

    public function __construct(Services_Stormpath_Client_ApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function executeRequest(Services_Stormpath_Http_Request $request)
    {

    }

}