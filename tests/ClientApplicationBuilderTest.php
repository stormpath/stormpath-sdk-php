<?php


class ClientApplicationBuilderTest extends PHPUnit_Framework_TestCase
{
    private $clientFile = 'client.yml';
    private $applicationHref = 'https://api.stormpath.com/v1/applications/fzyWJ5V_SDORGPk4fT2jhA';
    private $httpPrefix = 'https://';
    private $appHrefWithoutHttp = '@api.stormpath.com/v1/applications/fzyWJ5V_SDORGPk4fT2jhA';
    private $clientBuilder;

    public function setUp()
    {
        $this->clientBuilder = new Services_Stormpath_Client_ClientBuilder;
    }

    public function testReadDefaultPropertiesFromFile()
    {
        $builder = new Services_Stormpath_Client_ClientApplicationBuilder($this->clientBuilder);

        $result = $builder
                  ->setApplicationHref($this->applicationHref)
                  ->setApiKeyFileLocation($this->clientFile)
                  ->build()
                  ->getApplication();

        $className = 'Services_Stormpath_Resource_Application';

        $this->assertInstanceOf($className, $result);
    }

    public function testCreateClientApplicationFromApplicationHrefWithCredentials()
    {
        // getting the YAML content from file...just to avoid writing them directly
        // in the 'properties' string
        $extractFromYml = Services_Stormpath_Util_Spyc::YAMLLoad($this->clientFile);

        $apiKeyIdKeyword = 'apiKey.id';
        $apiKeySecretKeyword = 'apiKey.secret';

        $apiKeyId = Services_Stormpath_Util_YAMLUtil::retrieveNestedValue($extractFromYml, array($apiKeyIdKeyword));
        $apiKeySecret = Services_Stormpath_Util_YAMLUtil::retrieveNestedValue($extractFromYml, array($apiKeySecretKeyword));

        $applicationHref = $this->httpPrefix .
                           $apiKeyId .
                           ':' .
                           $apiKeySecret .
                           $this->appHrefWithoutHttp;

        $builder = new Services_Stormpath_Client_ClientApplicationBuilder($this->clientBuilder);

        $result = $builder
                  ->setApplicationHref($applicationHref)
                  ->build()
                  ->getApplication();

        $className = 'Services_Stormpath_Resource_Application';

        $this->assertInstanceOf($className, $result);

    }

    public function testReadCustomSimplePropertiesFromFile()
    {
        $this->clientBuilder = new Services_Stormpath_Client_ClientBuilder;
        $builder = new Services_Stormpath_Client_ClientApplicationBuilder($this->clientBuilder);

        $apiKeyIdKeyword = 'different.apiKey.id';
        $apiKeySecretKeyword = 'different.apiKey.secret';

        $result = $builder
                  ->setApplicationHref($this->applicationHref)
                  ->setApiKeyFileLocation($this->clientFile)
                  ->setApiKeyIdPropertyName($apiKeyIdKeyword)
                  ->setApiKeySecretPropertyName($apiKeySecretKeyword)
                  ->build()
                  ->getApplication();

        $className = 'Services_Stormpath_Resource_Application';

        $this->assertInstanceOf($className, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowExceptionWhenNoApplicationHrefPresent()
    {
        $builder = new Services_Stormpath_Client_ClientApplicationBuilder;

        $builder->build();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowExceptionWhenWrongClientBuilderProvided()
    {
        new Services_Stormpath_Client_ClientApplicationBuilder('WRONG');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowExceptionWhenWrongApplicationHref()
    {
        $builder = new Services_Stormpath_Client_ClientApplicationBuilder;

        $builder->setApplicationHref('id:secret@stormpath.com/v1')->build();
    }
}
