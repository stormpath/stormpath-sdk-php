<?php
/**
 * Created by PhpStorm.
 * User: bretterer
 * Date: 7/24/15
 * Time: 10:49 AM
 */

namespace Stormpath\Authc\Api;


use Stormpath\Resource\Account;
use Stormpath\Resource\ApiKey;
use Stormpath\Resource\Application;

class AuthenticationResult
{

    protected $application;

    protected $apiKey;

    public function __construct(Application $application, ApiKey $apiKey)
    {
        $this->application = $application;

        $this->apiKey = $apiKey;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }
}