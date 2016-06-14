<?php

namespace Stormpath;

/*
 * Copyright 2016 Stormpath, Inc.
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
use Stormpath\Cache\ArrayCacheManager;
use Stormpath\Cache\MemcachedCacheManager;
use Stormpath\Cache\NullCacheManager;
use Stormpath\Cache\RedisCacheManager;
use Stormpath\Cache\PSR6CacheManagerInterface;
use Stormpath\Cache\Exceptions\InvalidCacheManagerException;
use Stormpath\Http\Authc\SAuthc1RequestSigner;
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
    private $cacheManager = NULL;
    private $cacheManagerOptions = array();
    private $baseURL;
    private $authenticationScheme = Stormpath::SAUTHC1_AUTHENTICATION_SCHEME;

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

    public function setCacheManager($cacheManager)
    {
        if ($cacheManager instanceOf PSR6CacheManagerInterface) {
            $this->cacheManager = $cacheManager;
        } else {
            switch ($cacheManager) {
                case 'Array':
                    $this->cacheManager = new ArrayCacheManager;
                    break;
                case 'Memcached':
                    $this->cacheManager = new MemcachedCacheManager;
                    break;
                case 'Redis':
                    $this->cacheManager = new RedisCacheManager;
                    break;
                case 'Null':
                    $this->cacheManager = new NullCacheManager;
                    break;
                default: // Legacy cache
                    $this->cacheManager = $this->qualifyCacheManager($cacheManager);
            }
        }

        return $this;
    }

    public function setCacheManagerOptions(Array $cacheManagerOptions = array())
    {
        $this->cacheManagerOptions = $this->setCacheOptionsArray($cacheManagerOptions);
        if(!$this->cacheManager) {
            $this->setCacheManager($this->cacheManagerOptions['cachemanager']);
        }
        return $this;
    }

    /**
     * <p>
     * Allows you to define which authentication scheme to use for the Client.  By default, the SAuthc1
     * scheme will be used.  For environments that manipulate your applications request headers,
     * you would want to change this to basic.  Otherwise, the default option will be fine.
     * <p/>
     *
     * @param $authenticationScheme set the scheme you want to use for signing your request.
     *
     * @return the ClientBuilder instance for method chaining.
     *
     */
    public function setAuthenticationScheme($authenticationScheme)
    {
        $this->authenticationScheme = $authenticationScheme;
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

        if (!$this->cacheManager)
        {
            $this->setCacheManagerOptions();
        }


        $apiKeyId = $this->getRequiredPropertyValue($apiKeyProperties, 'apiKeyId', $this->apiKeyIdPropertyName);

        $apiKeySecret = $this->getRequiredPropertyValue($apiKeyProperties, 'apiKeySecret', $this->apiKeySecretPropertyName);

        $apiKey = new ApiKey($apiKeyId, $apiKeySecret);

        $signer = $this->resolveSigner();
        $requestSigner = new $signer;

        return new Client(
            $apiKey,
            $this->cacheManager,
            $this->cacheManagerOptions,
            $this->baseURL,
            $requestSigner
        );

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

    private function setCacheOptionsArray($overrides)
    {
        $defaults = array(
            'cachemanager' => 'Array', //Array, Memcached, Redis, Null, or the full namespaced CacheManager instance
            'memcached' => array(),
            'redis' => array(),
            'ttl' => 60, // This value is set in minutes
            'tti' => 120, // This value is set in minutes
            'regions' => array(
                'accounts' => array(
                    'ttl' => 60,
                    'tti' => 120
                ),
                'applications' => array(
                    'ttl' => 60,
                    'tti' => 120
                ),
                'directories' => array(
                    'ttl' => 60,
                    'tti' => 120
                ),
                'groups' => array(
                    'ttl' => 60,
                    'tti' => 120
                ),
                'tenants' => array(
                    'ttl' => 60,
                    'tti' => 120
                ),
            )
        );
        return array_replace($defaults, $overrides);
    }

    private function qualifyCacheManager($cacheManager)
    {
        $notCoreClass = true;

        if(class_exists($cacheManager))
            $notCoreClass = class_implements($cacheManager) == 'Stormpath\Cache\CacheManager';

        if(class_exists($cacheManager) && $notCoreClass) return $cacheManager;

        if(strpos($cacheManager, 'CacheManager')) {
            $cacheManagerPath = "{$cacheManager}";
        } else {
            $cacheManagerPath = "Stormpath\\Cache\\{$cacheManager}CacheManager";
        }


        if(class_exists($cacheManagerPath)) return $cacheManagerPath;

    }

    private function resolveSigner()
    {
        $signer = "\\Stormpath\\Http\\Authc\\" . $this->authenticationScheme . "RequestSigner";

        if(!class_exists($signer))
            throw new \InvalidArgumentException('Authentication Scheme is not supported.');

        return new $signer;

    }
}
