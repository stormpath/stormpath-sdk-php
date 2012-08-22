<?php
require_once 'HTTP/Request2.php';

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

class Services_Stormpath_Http_HttpClientRequestExecutor
    implements Services_Stormpath_Http_RequestExecutor
{
    private $apiKey;
    private $httpClient;
    private $signer;

    public function __construct(Services_Stormpath_Client_ApiKey $apiKey = null)
    {
        $this->httpClient = new HTTP_Request2;

        if ($apiKey)
        {
            $this->apiKey = $apiKey;
            $this->signer = new Services_Stormpath_Http_Authc_Sauthc1Signer;
            $this->httpClient->setConfig(array('ssl_verify_peer' => FALSE,
                                               'ssl_verify_host' => FALSE));
        }

    }

    public function executeRequest(Services_Stormpath_Http_Request $request)
    {
        if ($this->apiKey)
        {
            $this->signer->signRequest($request, $this->apiKey);
        }

        $this->httpClient->setUrl($request->getResourceUrl());

        $this->addQueryString($request->getQueryString());

        $this->httpClient->setMethod($request->getMethod());

        $this->httpClient->setBody($request->getBody());

        $this->httpClient->setHeader($request->getHeaders());

        $response = $this->httpClient->send();

        return new Services_Stormpath_Http_DefaultResponse($response->getStatus(),
                                                           $response->getHeader('Content-Type'),
                                                           $response->getBody(),
                                                           $response->getHeader('Content-Length'));

    }

    private function addQueryString(array $queryString)
    {
        ksort($queryString);

        foreach($queryString as $key => $value)
        {
            $this->httpClient->getUrl()->setQueryVariable($key, $value);
        }
    }

}
