<?php

namespace Stormpath\Http;

use Zend\Http\Request as Req;

interface RequestExecutor
{
    public function executeRequest(Request $request, $redirectsLimit = 10);

	public function zendExecuteRequest(Req $request, $redirectsLimit = 10);

}