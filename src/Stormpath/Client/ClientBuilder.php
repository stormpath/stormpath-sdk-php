<?php

namespace Stormpath\Client;

use Stormpath\Util\Spyc;
use Stormpath\Util\YAMLUtil;
use Stormpath\Client\ApiKey;
use Stormpath\Client\Client;
use Stormpath\Http\DefaultRequest;
use Stormpath\Http\Request;
use Stormpath\Http\HttpClientRequestExecutor;

class ClientBuilder
{
    private $apiKeyIdPropertyName = "apiKey.id";
    private $apiKeySecretPropertyName = "apiKey.secret";
    private $apiKeyProperties;
    private $apiKeyFileLocation;
    private $baseURL;

    public function setApiKeyFileLocation($apiKeyFileLocation)
    {
        $this->apiKeyFileLocation = $apiKeyFileLocation;
        return $this;
    }

    public function setApiKeyIdPropertyName($apiKeyIdPropertyName)
    {
        $this->apiKeyIdPropertyName = $apiKeyIdPropertyName;
        return $this;
    }

    public function setApiKeySecretPropertyName($apiKeySecretPropertyName)
    {
        $this->apiKeySecretPropertyName = $apiKeySecretPropertyName;
        return $this;
    }

    public function setApiKeyProperties($apiKeyProperties)
    {
        if (!is_string($apiKeyProperties))
        {
            throw new \InvalidArgumentException('The $apiKeyProperties argument must be a string');
        }

        $this->apiKeyProperties = $apiKeyProperties;
        return $this;
    }

    public function build()
    {
        $extractFromYml = null;

        if ($this->apiKeyProperties)
        {
            $extractFromYml = Spyc::YAMLLoadString($this->apiKeyProperties);
        } else
        {
            // need to load the properties file
            $file = $this->getAvailableFile();

            if (!$file)
            {
                throw new \InvalidArgumentException('No API Key file could be found or loaded from a file location. ' .
                    'Please  configure the "apiKeyFileLocation" property or alternatively configure a ' .
                    'YAML compliant string.');
            }

            $extractFromYml = Spyc::YAMLLoad($file);
        }

        $apiKeyId = $this->getRequiredPropertyValue($extractFromYml, 'apiKeyId', $this->apiKeyIdPropertyName);

        $apiKeySecret = $this->getRequiredPropertyValue($extractFromYml, 'apiKeySecret', $this->apiKeySecretPropertyName);

        if (!$apiKeyId)
        {
            throw new \InvalidArgumentException('$apiKeyId must have a value when acquiring it from the YAML extract');
        }

        if (!$apiKeySecret)
        {
            throw new \InvalidArgumentException('$apiKeySecret must have a value when acquiring it from the YAML extract');
        }

        $apiKey = new ApiKey($apiKeyId, $apiKeySecret);

        return new Client($apiKey, $this->baseURL);
    }

    public function setBaseURL($baseURL)
    {
        $this->baseURL = $baseURL;
        return $this;
    }

    private function getRequiredPropertyValue(array $extractFromYml, $masterName, $propertyName)
    {
        if (!is_array($propertyName))
        {
            $propertyName = array($propertyName);
        }

        $result = YAMLUtil::retrieveNestedValue($extractFromYml, $propertyName);

        if (YAMLUtil::NOT_NESTED_VALUE_FOUND == $result)
        {
            throw new \InvalidArgumentException("There is no '" . implode(':', $propertyName) . "' property in the " .
                "configured apiKey YAML.  You can either specify that property or " .
                "configure the " . $masterName . "PropertyName value on the ClientBuilde r to specify a " .
                "custom property name.");
        }

        return $result;
    }

    private function getAvailableFile()
    {
        if (stripos($this->apiKeyFileLocation, 'http') !== false)
        {
            $request = new DefaultRequest(Request::METHOD_GET,$this->apiKeyFileLocation);


            $executor = new HttpClientRequestExecutor;

            try {
                $response = $executor->executeRequest($request);

                if (!$response->isError())
                {
                    return $response->getBody();

                }
            } catch (Exception $e)
            {
                return null;
            }
        } else
        {
            if (file_exists($this->apiKeyFileLocation))
            {
                return $this->apiKeyFileLocation;
            }
        }
    }
}
