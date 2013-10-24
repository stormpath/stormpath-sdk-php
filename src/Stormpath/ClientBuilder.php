<?php

namespace Stormpath;

/*
 * Copyright 2013 Stormpath, Inc.
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
use Stormpath\Http\DefaultRequest;
use Stormpath\Http\HttpClientRequestExecutor;
use Stormpath\Http\Request;
use Stormpath\Util\Magic;
use Stormpath\Util\Spyc;
use Stormpath\Util\YAMLUtil;

/**
 * A <a href="http://en.wikipedia.org/wiki/Builder_pattern">Builder design pattern</a> implementation used to
 * construct {@link Stormpath\Client\Client} instances.
 * <p/>
 * The ClientBuilder is especially useful for constructing Client
 * instances with Stormpath API Key information loaded from an external <i>ini</i> file (or ini loadable string)
 * to ensure the API Key secret (password) does not reside in plaintext in code.
 * <p/>
 * Example usage:
 * <code>
 * $location = '/home/jsmith/.stormpath/apiKey.properties';
 *
 * $clientBuilder = new ClientBuilder;
 * $client = $clientBuilder->setApiKeyFileLocation($location)->build();
 * </code>
 * <p/>
 * You may load files from the filesystem or URLs by specifying the file location.
 *
 * @see setApiKeyFileLocation() for more information.
 */
class ClientBuilder extends Magic
{
    private $apiKeyIdPropertyName = "apiKey.id";
    private $apiKeySecretPropertyName = "apiKey.secret";
    private $apiKeyProperties;
    private $apiKeyFileLocation;
    private $baseURL;

    /**
     * Sets the location of the 'ini' file to load containing the API Key (Id and secret) used by the
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
     * Assuming you were using these default property names, your <i>ClientBuilder</i> usage might look like the
     * following:
     * <pre>
     * $location = "/home/jsmith/.stormpath/apiKey.properties";
     *
     * $clientBuilder = new ClientBuilder;
     * $client = $clientBuilder->setApiKeyFileLocation($location)->build();
     *
     * </pre>
     * <h3>Custom Property Names</h3>
     * If you want to control the property names used in the file, you may configure them via
     * {@link setApiKeyIdPropertyName} and
     * {@link setApiKeySecretPropertyName}.
     * <p/>
     * For example, if you had a <i>/home/jsmith/.stormpath/apiKey.properties</i> file with the following
     * name/value pairs:
     * <pre>
     * $myStormpathApiKeyId = 'foo'
     * $myStormpathApiKeySecret = 'mySuperSecretValue'
     * </pre>
     * Your <i>ClientBuilder</i> usage would look like the following:
     * <pre>
     * $location = "/home/jsmith/.stormpath/apiKey.properties";
     *
     * $clientBuilder = new ClientBuilder;
     * $client = $clientBuilder->setApiKeyFileLocation($location)
     *                         ->setApiKeyIdPropertyName($myStormpathApiKeyId)
     *                         ->setApiKeySecretPropertyName($myStormpathApiKeySecret)
     *                         ->build();
     * </pre>
     *
     * @param apiKeyFileLocation the file or url location of the API Key file to load when
     *                 constructing the API Key to use for communicating with the Stormpath REST API.
     *
     * @return the ClientBuilder instance for method chaining.
     */
    public function setApiKeyFileLocation($apiKeyFileLocation)
    {
        $this->apiKeyFileLocation = $apiKeyFileLocation;
        return $this;
    }

    /**
     * <p>
     * Sets the name used to query for the API Key Id from an ini content.
     *
     * The <b>$apiKeyIdPropertyName</b> must be a string.
     * </p>
     * <code>
     * //Example 1: Having the file 'apiKey.properties' with the following content:
     *
     *
     *           apiKey.id = myStormpathApiKeyId
     *
     * //The method should be called as follows:
     *
     *           $clientBuilder->setApiKeyIdPropertyName('apiKey.id');
     *
     * //Example 2: Having the file 'apiKey.properties' with the following content:
     *
     *           stormpath.apiKey.id = myStormpathApiKeyId
     *
     * //The method should be called as follows:
     *
     *           $clientBuilder->setApiKeyIdPropertyName('stormpath.apiKey.id');
     *
     * </code>
     * @param string $apiKeyIdPropertyName the name used to query for the API Key Id from an ini content.
     * @return the ClientBuilder instance for method chaining.
     */
    public function setApiKeyIdPropertyName($apiKeyIdPropertyName)
    {
        $this->apiKeyIdPropertyName = $apiKeyIdPropertyName;
        return $this;
    }

    /**
     * <p>
     * Sets the name used to query for the API Key Secret from an ini content.
     *
     * The <b>$apiKeySecretPropertyName</b> must be a string.
     * </p>
     * <code>
     * //Example 1: Having the file 'apiKey.properties' with the following content:
     *
     *           apiKey.secret = myStormpathApiKeySecret
     *
     * //The method should be called as follows:
     *
     *           $clientBuilder->setApiKeySecretPropertyName('apiKey.secret');
     *
     * //Example 2: Having the file 'apiKey.properties' with the following content:
     *
     *
     *           stormpath.apiKey.secret = myStormpathApiKeySecret
     *
     * //The method should be called as follows:
     *
     *           $clientBuilder->setApiKeySecretPropertyName('stormpath.apiKey.secret');
     *
     * </code>
     * @param string $apiKeySecretPropertyName the name used to query for the API Key Secret from an ini content.
     * @return the ClientBuilder instance for method chaining.
     */
    public function setApiKeySecretPropertyName($apiKeySecretPropertyName)
    {
        $this->apiKeySecretPropertyName = $apiKeySecretPropertyName;
        return $this;
    }

    /**
     * <p>
     * Allows usage of a PHP ini compliant string instead of loading a file via
     * {@link setApiKeyFileLocation} configuration.
     * <p/>
     * The string contents and property name overrides functions are the same as described in the
     * {@link setApiKeyFileLocation} API Documentation.
     *
     * @param $apiKeyProperties the PHP ini string to use to load the API Key ID and Secret.
     *
     * @return the ClientBuilder instance for method chaining.
     *
     */
    public function setApiKeyProperties($apiKeyProperties)
    {
        $this->apiKeyProperties = $apiKeyProperties;
        return $this;
    }

    /**
     * Constructs a new {@link Stormpath\Client\Client} instance based on the ClientBuilder's
     * current configuration state.
     *
     * @return a new Client instance based on the ClientBuilder's
     * current configuration state.
     */
    public function build()
    {
        $apiKeyProperties = null;

        if ($this->apiKeyProperties)
        {
            $apiKeyProperties = parse_ini_string($this->apiKeyProperties);

        } else
        {
            // need to load the properties file
            $apiKeyProperties = $this->getFileExtract();

            if (!$apiKeyProperties)
            {
                throw new \InvalidArgumentException('No API Key file could be found or loaded from a file location. ' .
                    'Please  configure the "apiKeyFileLocation" property or alternatively configure a ' .
                    "PHP 'ini' compliant string, by setting the 'apiKeyProperties' property.");
            }
        }

        $apiKeyId = $this->getRequiredPropertyValue($apiKeyProperties, 'apiKeyId', $this->apiKeyIdPropertyName);

        $apiKeySecret = $this->getRequiredPropertyValue($apiKeyProperties, 'apiKeySecret', $this->apiKeySecretPropertyName);

        $apiKey = new ApiKey($apiKeyId, $apiKeySecret);

        return new Client($apiKey, $this->baseURL);
    }

    public function setBaseURL($baseURL)
    {
        $this->baseURL = $baseURL;
        return $this;
    }

    private function getRequiredPropertyValue(array $apiKeyProperties, $masterName, $propertyName)
    {
        $result = array_key_exists($propertyName, $apiKeyProperties) ? $apiKeyProperties[$propertyName] : false;

        if (!$result)
        {
            throw new \InvalidArgumentException("There is no '$propertyName' property in the " .
                "configured apiKey file or properties string.  You can either specify that property or " .
                "configure the '$masterName' PropertyName value on the ClientBuilder to specify a " .
                "custom property name.");
        }

        return $result;
    }

    private function getFileExtract()
    {
        // @codeCoverageIgnoreStart
        if (stripos($this->apiKeyFileLocation, 'http') === 0)
        {
            $request = new DefaultRequest(Request::METHOD_GET, $this->apiKeyFileLocation);

            $executor = new HttpClientRequestExecutor;

            try {
                $response = $executor->executeRequest($request);

                if (!$response->isError())
                {
                    return parse_ini_string($response->getBody());

                }
            } catch (Exception $e)
            {
                return false;
            }
        }
        // @codeCoverageIgnoreEnd

        if ($this->apiKeyFileLocation)
        {
            return parse_ini_file($this->apiKeyFileLocation);
        }
    }
}