<?php

class ClientBuilderTest extends PHPUnit_Framework_TestCase
{
    private $clientFile = 'client.yml';
    private $clientURLFile = 'http://localhost/client.yml';

    public function testReadDefaultPropertiesFromLocalFile()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyFileLocation($this->clientFile)
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);
    }

    public function testFailureReadDefaultPropertiesFromLocalFile()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;

        try{

            $builder->setApiKeyFileLocation('wrongFile')
                     ->build();

            $this->assertTrue(false);
        } catch (InvalidArgumentException $iae)
        {
            $this->assertTrue(true);
        }
    }

    public function testReadCustomSimplePropertiesFromLocalFile()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyFileLocation($this->clientFile)
                  ->setApiKeyIdPropertyName('different.apiKey.id')
                  ->setApiKeySecretPropertyName('different.apiKey.secret')
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);
    }

    public function testFailureReadCustomSimplePropertiesFromLocalFile()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;

        try{

            $builder->setApiKeyFileLocation($this->clientFile)
                    ->setApiKeySecretPropertyName('wrongSecret')
                    ->build();

            $this->assertTrue(false);
        } catch (InvalidArgumentException $iae)
        {
            $this->assertTrue(true);
        }
    }

    public function testEmptyReadCustomSimplePropertiesFromLocalFile()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;

        try{

            $builder->setApiKeyFileLocation($this->clientFile)
                    ->setApiKeyIdPropertyName('empty.apiKey.id')
                    ->build();

            $this->assertTrue(false);
        } catch (InvalidArgumentException $iae)
        {
            $this->assertTrue(true);
        }
    }

    public function testReadDefaultPropertiesFromString()
    {
        // getting the YAML content from file...just to avoid writing them directly
        // in the 'properties' string
        $properties = file_get_contents($this->clientFile);

        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyProperties($properties)
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);
    }

    public function testFailureReadDefaultPropertiesFromString()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;

        try{

            $builder->setApiKeyProperties(array())
                    ->build();

            $this->assertTrue(false);
        } catch (InvalidArgumentException $iae)
        {
            $this->assertTrue(true);
        }
    }

    public function testReadCustomSimplePropertiesFromString()
    {
        // getting the YAML content from file...just to avoid writing them directly
        // in the 'properties' string
        $properties = file_get_contents($this->clientFile);

        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyIdPropertyName('different.apiKey.id')
                  ->setApiKeySecretPropertyName('different.apiKey.secret')
                  ->setApiKeyProperties($properties)
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);
    }

    public function testReadCustomComplexPropertiesFromLocalFile()
    {
        $idPath = array('stormpath', 'apiKey', 'id');
        $secretPath = array('stormpath', 'apiKey', 'secret');
        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyFileLocation($this->clientFile)
                  ->setApiKeyIdPropertyName($idPath)
                  ->setApiKeySecretPropertyName($secretPath)
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);
    }

    public function testReadCustomComplexPropertiesFromString()
    {
        // getting the YAML content from file...just to avoid writing them directly
        // in the 'properties' string
        $properties = file_get_contents($this->clientFile);
        $idPath = array('different.stormpath', 'different.apiKey', 'different.id');
        $secretPath = array('different.stormpath', 'different.apiKey', 'different.secret');

        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyIdPropertyName($idPath)
                  ->setApiKeySecretPropertyName($secretPath)
                  ->setApiKeyProperties($properties)
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);
    }

    public function testFailureReadDefaultPropertiesFromURLFile()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;

        try{

            $builder->setApiKeyFileLocation('http://localhost/badfile.yml')
                    ->build();

            $this->assertTrue(false);
        } catch (InvalidArgumentException $iae)
        {
            $this->assertTrue(true);
        }
    }


    public function testGetTenantWithCustomComplexPropertiesFromLocalFile()
    {
        $idPath = array('stormpath', 'apiKey', 'id');
        $secretPath = array('stormpath', 'apiKey', 'secret');
        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyFileLocation($this->clientFile)
                  ->setApiKeyIdPropertyName($idPath)
                  ->setApiKeySecretPropertyName($secretPath)
                  //->setBaseURL('http://localhost:8080/v1')
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);

        $result = $result->getCurrentTenant();

        $className = 'Services_Stormpath_Resource_Tenant';
        $this->assertInstanceOf($className, $result);
    }

    public function testGetTenantWithDefaultPropertiesFromURLFile()
    {
        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyFileLocation($this->clientURLFile)
                  //->setBaseURL('http://localhost:8080/v1')
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);

        $result = $result->getCurrentTenant();

        $className = 'Services_Stormpath_Resource_Tenant';
        $this->assertInstanceOf($className, $result);
    }

    public function testGetTenantWithCustomComplexPropertiesFromURLFile()
    {
        $idPath = array('different.stormpath', 'different.apiKey', 'different.id');
        $secretPath = array('different.stormpath', 'different.apiKey', 'different.secret');
        $builder = new Services_Stormpath_Client_ClientBuilder;
        $result = $builder->setApiKeyFileLocation($this->clientURLFile)
                  ->setApiKeyIdPropertyName($idPath)
                  ->setApiKeySecretPropertyName($secretPath)
                  //->setBaseURL('http://localhost:8080/v1')
                  ->build();

        $className = 'Services_Stormpath_Client_Client';
        $this->assertInstanceOf($className, $result);

        $result = $result->getCurrentTenant();

        $className = 'Services_Stormpath_Resource_Tenant';
        $this->assertInstanceOf($className, $result);
    }
}
