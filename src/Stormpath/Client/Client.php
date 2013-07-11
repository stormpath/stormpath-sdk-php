<?php

namespace Stormpath\Client;

use Stormpath\Client\ApiKey;
use Stormpath\Http\HttpClientRequestExecutor;
use Stormpath\DataStore\DefaultDataStore;
use Stormpath\Service\StormpathService;
use Stormpath\Http\DefaultRequest;
use Stormpath\Http\Request;
use Zend\Config\Reader;

class Client
{
	private $apiKey;
	public $dataStore;
	public $apiKeyFileLocation;

	public function __construct($options = array())
	{
		$apiKey = $options['apiKey'];
		$baseUrl = $options['baseUrl'];
		$this->apiKeyFileLocation = $options['apiKeyFileLocation']; //'~/.stormpath/apikey.yml'

		if(!empty($apiKey)) {
			if(is_array($apiKey)) {
				$this->apiKey = new ApiKey($apiKey['id'],$apiKey['secret']);
			}
			else if ($apiKey instanceof ApiKey) {
				$this->apiKey = $apiKey;
			}
		}
		else if($this->apiKeyFileLocation) {
			$this->loadApiKeyFile($this->getApiKeyFileLocation());
		}

		if (!$this->apiKey->getId()) {
			throw new \InvalidArgumentException('$apiKeyId must have a value when acquiring it from the YAML extract');
		}

		if (!$this->apiKey->getSecret()) {
			throw new \InvalidArgumentException('$apiKeySecret must have a value when acquiring it from the YAML extract');
		}

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

	public function getApiKeyFileLocation()
	{
		return $this->apiKeyFileLocation;
	}

	private function loadApiKeyFile($apiKeyFileLocation, $idPropertyName = 'apiKey.id', $secretPropertyName = 'apiKey.secret')
	{
		$reader = new Reader\Yaml();
		$data   = $reader->fromFile($apiKeyFileLocation);

		return new ApiKey($data[$idPropertyName], $data[$secretPropertyName]);
	}


}