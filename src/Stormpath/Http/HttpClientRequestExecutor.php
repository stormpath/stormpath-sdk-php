<?php

namespace Stormpath\Http;

use Zend\Http\Client;
use Zend\Http\Request as Req;
use Zend\Http\Response as Res;
use Stormpath\Http\Request;
use Stormpath\Client\ApiKey;
use Zend\Json\Json;
use Stormpath\Authc\Digest;
use Stormpath\Authc\Basic;
use Stormpath\Service\StormpathService;

class HttpClientRequestExecutor implements RequestExecutor
{
	public $httpClient;
	public $apiKey;

    public function __construct(ApiKey $apiKey = null)
    {
        $this->httpClient = new Client();

        if ($apiKey)
        {
            $this->apiKey = $apiKey;
			//need to make a provision for the users to choose the authentication type
			$adapter = new Basic($this->apiKey);
			$this->httpClient->setAdapter($adapter);

            $this->httpClient->setOptions(array('ssl_verify_peer' => FALSE,
                                               'ssl_verify_host' => FALSE));
        }

    }
	/*
	 * This method uses the Zend Request and Response class
	 * It does not use any of the existing Http classes
	 */
	public function zendExecuteRequest(Req $request, $redirectsLimit = 10)
	{
		$this->httpClient->setUri($request->getUriString());
		$this->addQueryString($request->getQuery());
		$this->httpClient->setMethod($request->getMethod());
		$this->httpClient->setRawBody($request->getContent());
		$this->httpClient->setHeaders($request->getHeaders());
		$response = $this->httpClient->send();
		if ($response->isRedirect() && $redirectsLimit)
		{
			$request->setUri($response->getHeaders('location'));
			return $this->zendExecuteRequest($request, --$redirectsLimit);
		}

		return new Res($response->getStatusCode(),
					   $response->getHeaders('Content-Type'),
					   $response->getBody(),
					   $response->getHeaders('Content-Length'));
	}

	/*
	 * This method uses the existing Http classes
	 * Implements the Request and Response Interface
	 */
    public function executeRequest(Request $request, $redirectsLimit = 10)
    {

        $this->httpClient->setUri($request->getResourceUrl());

        $this->addQueryString($request->getQueryString());

        $this->httpClient->setMethod($request->getMethod());

        $this->httpClient->setRawBody($request->getBody());

        $this->httpClient->setHeaders($request->getHeaders());

        $response = $this->httpClient->send();

        if ($response->isRedirect() && $redirectsLimit)
        {
            $request->setResourceUrl($response->getHeaders('location'));
            return $this->executeRequest($request, --$redirectsLimit);

        }

        return new DefaultResponse($response->getStatusCode(),
                                   $response->getHeaders('Content-Type'),
                                   $response->getBody(),
                                   $response->getHeaders('Content-Length'));

    }

    private function addQueryString(array $queryString)
    {
        ksort($queryString);

        foreach($queryString as $key => $value)
        {
            $this->httpClient->getUri()->setQuery($key, $value);
        }
    }

}
