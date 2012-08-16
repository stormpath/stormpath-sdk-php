<?php
require_once('../Services/Stormpath/Client/ClientBuilder.php');

class ClientBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testReadDefaultProperties()
    {
        $yamlArray = Services_Stormpath_Util_Spyc::YAMLLoad('client.yml');

        $arrayWithKeys = array('different.stormpath', 'different.apiKey', 'different.id');

        echo retrieveNestedValue($yamlArray, $arrayWithKeys);
    }
}
