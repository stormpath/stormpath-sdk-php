<?php
/**
 *
 */

namespace Stormpath\Client;

use Stormpath\Http\RequestExecutor;
use Stormpath\DataStore\DataStore;
use Stormpath\Client\ApiKey;
use Zend\Config\Reader;

class Client
{
	private $apiKey;
	private $baseURL;
	private $datastore;

	public function __construct($options = array())
	{
		$apiKey =	$options['apiKey'];
		$baseURL = $options['baseURL'];
		$apiKeyFileLocation = $options['apiKeyFileLocation'];

		if(!empty($apiKey)) {
			if(is_array($apiKey)) {
				$this->apiKey = new ApiKey($apiKey['id'],$apiKey['secret']);
			}
			else if ($apiKey instanceof ApiKey) {
				$this->apiKey = $apiKey;
			}
		}
		else if($apiKeyFileLocation) {
			$this->loadApiKeyFile('~/.stormpath/apikey.yml');
		}

		if (!$this->apiKey->getId()) {
			throw new InvalidArgumentException('$apiKeyId must have a value when acquiring it from the YAML extract');
		}

		if (!$this->apiKey->getSecret()) {
			throw new InvalidArgumentException('$apiKeySecret must have a value when acquiring it from the YAML extract');
		}

		$this->baseURL = $baseURL;
	  	$requestExecutor = new RequestExecutor($apiKey);
	  	$this->datastore = new DataStore($requestExecutor, $this, $baseURL);
	}

	public function getCurrentTenant()
	{
		$this->datastore->getResource('/tenants/current','Tenant');
	}

	private function loadApiKeyFile($apiKeyFileLocation, $idPropertyName = 'apiKey.id', $secretPropertyName = 'apiKey.secret')
	{
		$reader = new Reader\Yaml();
		$data   = $reader->fromFile($apiKeyFileLocation);

	    return new ApiKey($data[$idPropertyName], $data[$secretPropertyName]);
	}
}