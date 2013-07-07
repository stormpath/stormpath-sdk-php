<?php
/**
 *
 */

namespace Stormpath\DataStore;

use Stormpath\Http\Requestexecutor;
use Stormpath\Service\StormpathService as Stormpath;
use Stormpath\Client\Client;

class DataStore implements \DataStoreInterface
{
    private $requestExecutor;
	private $baseURL;
	private $client;

    public function __construct(RequestExecutor $requestExecutor, Client $client, $baseURL)
    {
        $this->requestExecutor = $requestExecutor;
		$this->client = $client;
		$this->baseURL = $baseURL;
    }

	public function instantiate($classname, $properties = array())
	{

	}

	public function getResource($href, $classname, $query='')
	{
		$href = Stormpath::BASEURI .'/';

		if ($this->needsToBeFullyQualified($href))
		{
			$href = $this->qualify($href);
		}

		$data = $this->executeRequest('GET', $href);

		return $this->instantiate($classname, array($data));


	}

    public function create($parenthref, $resouce, $returnType)
    {

    }

    public function save($resource, $classname)
    {

    }

    public function delete($resource)
    {

    }

	public function executeRequest($method, $href, $body = 'null', $query = 'null')
	{
		$this->requestExecutor->executeRequest($method,$href,$body);
		//$result =
	}

	protected function needsToBeFullyQualified($href)
	{
		return stripos($href, 'http') === false;
	}

	protected function qualify($href)
	{
		$slashAdded = '';

		if (!(stripos($href, '/') == 0))
		{
			$slashAdded = '/';
		}

		return $this->baseURL .$slashAdded .$href;
	}



}