<?php
/**
 * A wrapper to execute the requests
 */

namespace Stormpath\http;

use Zend\Http\Client;
use Stormpath\Client\ApiKey;
use Zend\Json\Json;
use Stormpath\Auth\Digest;
use Stormpath\Auth\Basic;
use Stormpath\Service\StormpathService as Stormpath;

class RequestExecutor
{
    public $httpClient;
	public $apikey;

	public function __Construct(ApiKey $apikey)
	{
		$this->apikey = $apikey;
	}

    public function getHttpClient()
    {

        return $this->httpClient;
    }

    public function setHttpClient(Client $value)
    {
       $value->setOptions(array('sslverifypeer' => false));
       $this->httpClient = $value;
    }

    public  function executeRequest($method,$href,$requiredParameters = array(),$optionalParameters = array())
    {
        // Set default http client; overwriteable after configuration
        $client = new Client();
        $adapter = new Basic($this->apikey);
        $client->setAdapter($adapter);
        $this->setHttpClient($client);

		$client = self::getHttpClient();
		$client->setUri($href);
		$client->setMethod($method);

		$parameters = array_merge($requiredParameters,$optionalParameters);

		$client->setRawBody(Json::encode($parameters));

		return $client->send();

    }

}