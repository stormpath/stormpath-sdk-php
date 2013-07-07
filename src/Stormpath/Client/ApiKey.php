<?php
/**
 * Getters and Setters for the API Key
 */

namespace Stormpath\Client;

class ApiKey
{

    private  $id;
    private  $secret;

	public function __construct($id, $secret)
	{
		$this->id = $id;
		$this->secret = $secret;
	}

    public function getId()
    {

        return $this->id;
    }

    public function getSecret()
    {

        return $this->secret;
    }

}