<?php
require_once 'TestResource.php';

class ResourceTest extends PHPUnit_Framework_TestCase
{

    public function testNonMaterializedResourceGetDirtyPropertyDoesNotMaterialize()
    {

        $properties = new stdClass();
        $properties->href = 'http://foo.com/test/123';

        $dataStore = new Services_Stormpath_DataStore_DefaultDataStore(new Services_Stormpath_Http_HttpClientRequestExecutor());

        $testResource = new TestResource($dataStore, $properties);
        $name = 'New Name';
        $testResource->setName($name);

        $this->assertTrue($name == $testResource->getName());
    }
}
