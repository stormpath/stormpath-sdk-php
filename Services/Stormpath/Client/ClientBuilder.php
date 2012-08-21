<?php

/*
 * Copyright 2012 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * A <a href="http://en.wikipedia.org/wiki/Builder_pattern">Builder design pattern</a> implementation used to
 * construct {@link Services_Stormpath_Client_Client} instances.
 * <p/>
 * The ClientBuilder is especially useful for constructing Services_Stormpath_Client_ClientBuilder
 * instances with Stormpath API Key information loaded from an external {@code .yml} file (or YAML loadable string)
 * to ensure the API Key secret (password) does not reside in plaintext in code.
 * <p/>
 * Example usage:
 * <code>
 * $location = '/home/jsmith/.stormpath/apiKey.yml';
 *
 * $clientBuilder = new Services_Stormpath_Client_ClientBuilder;
 * $client = $clientBuilder->setApiKeyFileLocation($location)->build();
 * </code>
 * <p/>
 * You may load files from the filesystem or URLs by specifying the file location.
 *
 * @see setApiKeyFileLocation() for more information.
 */
class Services_Stormpath_Client_ClientBuilder
{
    private $apiKeyIdPropertyName = "apiKey.id";
    private $apiKeySecretPropertyName = "apiKey.secret";
    private $apiKeyProperties;
    private $apiKeyFileLocation;
    private $baseURL;

    /**
     * Sets the location of the YAML file to load containing the API Key (Id and secret) used by the
     * Client to communicate with the Stormpath REST API.
     * <p/>
     * You may load files from the filesystem, or URLs just specifying the file location.
     * <h3>File Contents</h3>
     * <p/>
     * When the file is loaded, the following name/value pairs are expected to be present by default:
     * <table>
     *     <tr>
     *         <th>Key</th>
     *         <th>Value</th>
     *     </tr>
     *     <tr>
     *         <td>apiKey.id</td>
     *         <td>An individual account's API Key ID</td>
     *     </tr>
     *     <tr>
     *         <td>apiKey.secret</td>
     *         <td>The API Key Secret (password) that verifies the paired API Key ID.</td>
     *     </tr>
     * </table>
     * <p/>
     * Assuming you were using these default property names, your {@code ClientBuilder} usage might look like the
     * following:
     * <pre>
     * $location = "/home/jsmith/.stormpath/apiKey.yml";
     *
     * $clientBuilder = new Services_Stormpath_Client_ClientBuilder;
     * $client = $clientBuilder->setApiKeyFileLocation($location)->build();
     *
     * </pre>
     * <h3>Custom Property Names</h3>
     * If you want to control the property names used in the file, you may configure them via
     * {@link setApiKeyIdPropertyName} and
     * {@link setApiKeySecretPropertyName}.
     * <p/>
     * For example, if you had a {@code /home/jsmith/.stormpath/apiKey.yml} file with the following
     * name/value pairs:
     * <pre>
     * $myStormpathApiKeyId = 'foo'
     * $myStormpathApiKeySecret = 'mySuperSecretValue'
     * </pre>
     * Your {@code ClientBuilder} usage would look like the following:
     * <pre>
     * $location = "/home/jsmith/.stormpath/apiKey.yml";
     *
     * $clientBuilder = new Services_Stormpath_Client_ClientBuilder;
     * $client = $clientBuilder->setApiKeyFileLocation($location)
     *                         ->setApiKeyIdPropertyName($myStormpathApiKeyId)
     *                         ->setApiKeySecretPropertyName($myStormpathApiKeySecret)
     *                         ->build();
     * </pre>
     *
     * @param location the file or url location of the API Key {@code .yml} file to load when
     *                 constructing the API Key to use for communicating with the Stormpath REST API.
     *
     * @return the Services_Stormpath_Client_ClientBuilder instance for method chaining.
     */
    public function setApiKeyFileLocation($apiKeyFileLocation)
    {
        $this->apiKeyFileLocation = $apiKeyFileLocation;
        return $this;
    }

    /**
     * <p>
     * Sets the name used to query for the API Key ID from a YAML content.
     *
     * The <b>$apiKeyIdPropertyName</b> key can be a single name or a composed name, as deep as needed,
     * as long as it comes in the exact path order. If it's a single name, it must be specified as a string;
     * if it's a composed name, it must be specified in an array.
     * </p>
     * <code>
     * //Example 1: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           apiKey.id: myStormpathApiKeyId
     *
     * //The method should be called as follows:
     *
     *           $clientBuilder->setApiKeyIdPropertyName('apiKey.id');
     *
     * //Example 2: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           stormpath:
     *             apiKey:
     *             id: myStormpathApiKeyId
     *
     * //The method should be called as follows:
     *
     *           $keys = array('stormpath', 'apiKey', 'id');
     *           $clientBuilder->setApiKeyIdPropertyName($keys);
     *
     * </code>
     * @param string|array $apiKeyIdPropertyName the name used to query for the API Key ID from a YAML content.
     * @return the Services_Stormpath_Client_ClientBuilder instance for method chaining.
     */
    public function setApiKeyIdPropertyName($apiKeyIdPropertyName)
    {
        $this->apiKeyIdPropertyName = $apiKeyIdPropertyName;
        return $this;
    }

    /**
     * <p>
     * Sets the name used to query for the API Key Secret from a YAML content.
     *
     * The <b>$apiKeySecretPropertyName</b> key can be a single name or a composed name, as deep as needed,
     * as long as it comes in the exact path order. If it's a single name, it must be specified as a string;
     * if it's a composed name, it must be specified in an array.
     * </p>
     * <code>
     * //Example 1: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           apiKey.secret: myStormpathApiKeySecret
     *
     * //The method should be called as follows:
     *
     *           $clientBuilder->setApiKeySecretPropertyName('apiKey.secret');
     *
     * //Example 2: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           stormpath:
     *             apiKey:
     *             secret: myStormpathApiKeySecret
     *
     * //The method should be called as follows:
     *
     *           $keys = array('stormpath', 'apiKey', 'secret');
     *           $clientBuilder->setApiKeySecretPropertyName($keys);
     *
     * </code>
     * @param string|array $apiKeySecretPropertyName the name used to query for the API Key Secret from a YAML content.
     * @return the Services_Stormpath_Client_ClientBuilder instance for method chaining.
     */
    public function setApiKeySecretPropertyName($apiKeySecretPropertyName)
    {
        $this->apiKeySecretPropertyName = $apiKeySecretPropertyName;
        return $this;
    }

    /**
     * <p>
     * Allows usage of a YAML compliant string instead of loading a YAML file via
     * {@link setApiKeyFileLocation} configuration.
     * <p/>
     * The YAML contents and property name overrides function the same as described in the
     * {@link setApiKeyFileLocation} API Documentation.
     *
     * @param $apiKeyProperties the YAML string to use to load the API Key ID and Secret.
     *
     * @return the Services_Stormpath_Client_ClientBuilder instance for method chaining.
     *
     */
    public function setApiKeyProperties($apiKeyProperties)
    {
        if (!is_string($apiKeyProperties))
        {
            throw new InvalidArgumentException('The $apiKeyProperties argument must be a string');
        }

        $this->apiKeyProperties = $apiKeyProperties;
        return $this;
    }

    /**
     * Constructs a new {@link Services_Stormpath_Client_Client} instance based on the ClientBuilder's
     * current configuration state.
     *
     * @return a new {@link Services_Stormpath_Client_Client} instance based on the ClientBuilder's
     * current configuration state.
     */
    public function build()
    {
        $extractFromYml = null;

        if ($this->apiKeyProperties)
        {
            $extractFromYml = Services_Stormpath_Util_Spyc::YAMLLoadString($this->apiKeyProperties);
        } else
        {
            // need to load the properties file
            $file = $this->getAvailableFile();

            if (!$file)
            {
                throw new InvalidArgumentException('No API Key file could be found or loaded from a file location. ' .
                    'Please  configure the "apiKeyFileLocation" property or alternatively configure a ' .
                    'YAML compliant string.');
            }

            $extractFromYml = Services_Stormpath_Util_Spyc::YAMLLoad($file);
        }

        $apiKeyId = $this->getRequiredPropertyValue($extractFromYml, 'apiKeyId', $this->apiKeyIdPropertyName);

        $apiKeySecret = $this->getRequiredPropertyValue($extractFromYml, 'apiKeySecret', $this->apiKeySecretPropertyName);

        if (!$apiKeyId)
        {
            throw new InvalidArgumentException('$apiKeyId must have a value when acquiring it from the YAML extract');
        }

        if (!$apiKeySecret)
        {
            throw new InvalidArgumentException('$apiKeySecret must have a value when acquiring it from the YAML extract');
        }

        $apiKey = new Services_Stormpath_Client_ApiKey($apiKeyId, $apiKeySecret);

        return new Services_Stormpath_Client_Client($apiKey, $this->baseURL);
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

        $result = Services_Stormpath_Util_YAMLUtil::retrieveNestedValue($extractFromYml, $propertyName);

        if (Services_Stormpath_Util_YAMLUtil::NOT_NESTED_VALUE_FOUND == $result)
        {
            throw new InvalidArgumentException("There is no '" . implode(':', $propertyName) . "' property in the " .
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
            $request = new Services_Stormpath_Http_DefaultRequest(
                       Services_Stormpath_Http_Request::METHOD_GET,
                       $this->apiKeyFileLocation);

            $executor = new Services_Stormpath_Http_HttpClientRequestExecutor;

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