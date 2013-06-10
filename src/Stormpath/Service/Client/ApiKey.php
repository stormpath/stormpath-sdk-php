<?php
/*
 *  A Class to get the API key and Secret from the user
 */
namespace Stormpath\Client;

class ApiKey
{
    private $id;
    private $secret;

    public function __construct($id,$secret)
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
?>
