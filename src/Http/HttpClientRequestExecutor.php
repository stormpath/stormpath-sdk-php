<?php

namespace Stormpath\Http;


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

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
use Stormpath\ApiKey;
use Stormpath\Http\Authc\Sauthc1Signer;

class HttpClientRequestExecutor implements RequestExecutor
{
    private $apiKey;
    private $httpClient;
    private $signer;

    public function __construct(ApiKey $apiKey = null)
    {
        $this->httpClient = new Client();

        if ($apiKey)
        {
            $this->apiKey = $apiKey;
            $this->signer = new Sauthc1Signer;
            $this->httpClient->setConfig(array(Client::REQUEST_OPTIONS => array(
                                                   'allow_redirects' => false,
                                                   'exceptions' => false, // do not throw exceptions from the client
                                                   'verify' => false // do not verify SSL certificate
                                         )));
        }

    }

    public function executeRequest(Request $request, $redirectsLimit = 10)
    {
        $requestHeaders = $request->getHeaders();
        if ($this->apiKey)
        {
            $this->signer->signRequest($request, $this->apiKey);
            $this->httpClient->setUserAgent($requestHeaders['User-Agent']);
        }

        $httpRequest = $this->httpClient->
                        createRequest(
                            $method = $request->getMethod(),
                            $uri = $request->getResourceUrl(),
                            $headers = $request->getHeaders(),
                            $body = $request->getBody());

        $this->addQueryString($request->getQueryString(), $httpRequest);

        $response = $httpRequest->send();

        if ($response->isRedirect() && $redirectsLimit)
        {
            $request->setResourceUrl($response->getHeader('location'));
            return $this->executeRequest($request, --$redirectsLimit);

        }

        return new DefaultResponse($response->getStatusCode(),
                                   $response->getContentType(),
                                   $response->getBody(true),
                                   $response->getContentLength());

    }

    private function addQueryString(array $queryString, RequestInterface $request)
    {
        ksort($queryString);

        foreach($queryString as $key => $value)
        {
            $request->getQuery()->set($key, $value);
        }
    }

}
