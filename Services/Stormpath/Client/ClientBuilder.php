<?php

class Services_Stormpath_Client_ClientBuilder
{
    private $apiKeyIdPropertyName = "apiKey.id";
    private $apiKeySecretPropertyName = "apiKey.secret";

}

function retrieveNestedValue(array $arrayToSearch, array $keys)
{
    $obj = (object) $arrayToSearch;

    foreach($keys as $key)
    {
        if ($obj instanceof stdClass)
        {
            $obj = $obj->$key;

        } elseif (is_array($obj))
        {
            $obj = (object) $obj;
            $obj = $obj->$key;
        }
    }

    return $obj;
}