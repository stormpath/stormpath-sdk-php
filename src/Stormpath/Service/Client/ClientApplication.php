<?php
namespace Stormpath\Client;

class ClientApplication
{
    private $client;
    private $application;

    public function __construct(Client $client,Application $application)
    {
        $this->client = $client;
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function getClient()
    {
        return $this->client;
    }

}
