<?php
require_once 'HTTP/Request2.php';

class Services_Stormpath_Http_HttpClientRequestExecutor
    implements Services_Stormpath_Http_RequestExecutor
{
    private $apiKey;
    private $httpClient;

    public function __construct(Services_Stormpath_Client_ApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new HTTP_Request2;
    }

    public function executeRequest(Services_Stormpath_Http_Request $request)
    {

        //TODO: Switch to Digest Authentication
        $this->httpClient->setAuth($this->apiKey->getId(), $this->apiKey->getSecret());

        $this->addQueryString($request->getResourceUrl(), $request->getQueryString());

        $this->httpClient->setUrl($request->getResourceUrl());

        $this->httpClient->setMethod($request->getMethod());

        $this->httpClient->setBody($request->getBody());

        $this->httpClient->setHeader($request->getHeaders());

        $response = $request->send();

        return new Services_Stormpath_Http_DefaultResponse($response->getStatus(),
                                                           $response->getHeader('Content-Type'),
                                                           $response->getBody(),
                                                           $response->getHeader('Content-Length'));

    }

    private function addQueryString(String $href, array $queryString)
    {
        $orderedQS = ksort($queryString);

        foreach($orderedQS as $pair)
        {
            if (strpos($href, '?'))
            {
                $href  .= '&' . key($pair) . '=' . $pair;

            } else
            {
                $href  .= '?' . key($pair) . '=' . $pair;

            }

        }
    }

}