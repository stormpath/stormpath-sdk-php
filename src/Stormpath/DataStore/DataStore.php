<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vganesh
 * Date: 7/2/13
 * Time: 11:53 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\DataStore;

use Stormpath\Http\Requestexecutor;
use Stormpath\Service\StormpathService;

class DataStore implements \DataStoreInterface
{
    private $requestExecutor;

    public function __construct(RequestExecutor $requestExecutor)
    {
        $this->requestExecutor = $requestExecutor;
    }

	public function getResource($href, $classname, $query='')
	{
		$href = Stormpath::BASEURI .'/';

	}

    public function create()
    {
		//$this->requestExecutor->executeRequest('POST','directories');
    }

    public function save()
    {

    }

    public function delete()
    {

    }

	public function executeRequest()
	{

	}
}