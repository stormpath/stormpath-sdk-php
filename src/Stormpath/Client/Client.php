<?php
/**
 *
 */

namespace Stormpath\Client;

use Stormpath\Client\ApiKey;
use Stormpath\Client\Proxy;
use Stormpath\DataStore\DataStore;
use Zend\Navigation\Exception\InvalidArgumentException;

class client implements DataStore
{
	const DEFAULT_API_VERSION = 1;
	private $currentTenantHref;
	private $dataStore;

	public function __construct(Apikey $apiKey, $proxy = null)
	{
		if ($apiKey == null) {
			throw new InvalidArgumentException("apiKey argument cannot be null");
		}
		$requestExecutor = createRequestExecutor($apiKey,$proxy);
		$this->dataStore = createDataStore($requestExecutor, self::DEFAULT_API_VERSION);
	}

	public function getCurrentTenant()
	{
		$href = $this->currentTenantHref;
		if ($href == null) {
			$href = "/tenants/current";
		}
			$current = $this->dataStore->getResource($href, 'Tenant');
			$this->currentTenantHref = $current->getHref();
			return $current;

	}

	public function getDataStore()
	{
		return $this->dataStore();
	}

	private function createRequestExecutor(Apikey $apiKey, Proxy $proxy)
	{
		$className = "Stormpath\\Client\\http\\httpclient\\HttpClientRequestExecutor";

	}

	public function createDataStore($requestExecutor, $secondCtorArg)
	{

	}



}