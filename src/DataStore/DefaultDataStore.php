<?php

namespace Stormpath\DataStore;

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
use Stormpath\Http\Request;
use Stormpath\Http\RequestExecutor;
use Stormpath\Resource\Error;
use Stormpath\Resource\Resource;
use Stormpath\Resource\ResourceError;
use Stormpath\Util\Version;

class DefaultDataStore implements InternalDataStore
{

    private $requestExecutor;
    private $resourceFactory;
    private $baseUrl;

    const DEFAULT_SERVER_HOST = 'api.stormpath.com';
    const DEFAULT_API_VERSION = '1';

    public function __construct(RequestExecutor $requestExecutor, $baseUrl = null)
    {
        $this->requestExecutor = $requestExecutor;
        $this->resourceFactory = new DefaultResourceFactory($this);

        if(!$baseUrl)
        {
            $this->baseUrl = 'https://' . self::DEFAULT_SERVER_HOST . "/v" . self::DEFAULT_API_VERSION;

        } else
        {
           $this->baseUrl = $baseUrl;
        }

    }

    /**
     * Instantiates and returns a new instance of the specified Resource type name.  The instance is merely instantiated
     * and is not saved/synchronized with the server in any way.
     * <p/>
     * This method effectively replaces the <i>new</i> keyword that would have been used otherwise if the concrete
     * implementation was known (Resource implementation classes are intentionally not exposed to SDK end-users).
     *
     * @param $className the Resource class name (as a String) to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     * @param object $properties the optional Properties of the Resource to instantiate.
     * @param array options the options to create the resource. This optional argument is useful to specify query strings,
     * among other options.
     *
     * @return a new instance of the specified Resource.
     */
    public function instantiate($className, \stdClass $properties = null, array $options = array())
    {
        $propertiesArr = array($properties, $options);

       return $this->resourceFactory->instantiate($className, $propertiesArr);
    }

    /**
     * Looks up (retrieves) the resource at the specified {@code href} URL and returns the resource as an instance
     * of the specified {@code class} name.
     * <p/>
     * The <i>$className</i> argument must represent the name of an interface that is a sub-interface of
     * <i>Resource</i>, for example {@link Stormpath\Resource\Account}, {@link Stormpath\Resource\Directory}, etc.
     *
     * @param href  the resource URL of the resource to retrieve
     * @param class the <i>Resource</i> sub-interface to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     * @param options the options to create the resource. This optional argument is useful to specify query strings,
     * among other options.
     *
     * @return an instance of the specified class based on the data returned from the specified <i>href</i> URL.
     */
    public function getResource($href, $className, array $options = array())
    {
        if ($this->needsToBeFullyQualified($href))
        {
            $href = $this->qualify($href);
        }

        $queryString = $this->getQueryString($options);
        $data = $this->executeRequest(Request::METHOD_GET, $href, '', $queryString);

        return $this->resourceFactory->instantiate($className, array($data, $queryString));
    }

    public function create($parentHref, Resource $resource, $returnType, array $options = array())
    {
        $queryString = $this->getQueryString($options);
        $returnedResource = $this->saveResource($parentHref, $resource, $returnType, $queryString);

        $returnTypeClass = $this->resourceFactory->instantiate($returnType, array());
        if ($resource instanceof $returnTypeClass)
        {
            //ensure the caller's argument is updated with what is returned from the server:
            $resource->setProperties($this->toStdClass($returnedResource));
        }

        return $returnedResource;
    }

    public function save(Resource $resource, $returnType = null)
    {
        $href = $resource->getHref();

        if (!strlen($href))
        {
            throw new InvalidArgumentException('save may only be called on objects that have already been persisted (i.e. they have an existing href).');
        }

        if ($this->needsToBeFullyQualified($href))
        {
            $href = $this->qualify($href);
        }

        $returnType = $returnType ? $returnType : get_class($resource);

        $returnedResource = $this->saveResource($href, $resource, $returnType);

        //ensure the caller's argument is updated with what is returned from the server:
        $resource->setProperties($this->toStdClass($returnedResource));

        return $returnedResource;

    }

    public function delete(Resource $resource)
    {
        return $this->executeRequest(Request::METHOD_DELETE, $resource->getHref());
    }

    protected function needsToBeFullyQualified($href)
    {
        return stripos($href, 'http') === false;
    }

    protected function qualify($href)
    {
        $slashAdded = '';

        if (!(stripos($href, '/') == 0))
        {
            $slashAdded = '/';
        }

        return $this->baseUrl .$slashAdded .$href;
    }

    private function executeRequest($httpMethod, $href, $body = '', array $query = array())
    {
        $request = new DefaultRequest(
                       $httpMethod,
                       $href,
                       $query,
                       array(),
                       $body,
                       strlen($body));

        $this->applyDefaultRequestHeaders($request);

        $response = $this->requestExecutor->executeRequest($request);

        $result = $response->getBody() ? json_decode($response->getBody()) : '';

        if ($response->isError())
        {
            $errorResult = $result;

            //if the response does not come with a body, we create the error with the http status
            if (!$errorResult) {
                // @codeCoverageIgnoreStart
                $status = $response->getHttpStatus();
                $errorResult = new \stdClass();
                $errorResult->$status = $status;
                // @codeCoverageIgnoreEnd
            }

            $error = new Error($errorResult);
            throw new ResourceError($error);
        }

        return $result;

    }

    private function saveResource($href, Resource $resource, $returnType, array $query = array())
    {
        if ($this->needsToBeFullyQualified($href))
        {
            $href = $this->qualify($href);
        }

        $response = $this->executeRequest(Request::METHOD_POST,
                                          $href,
                                          json_encode($this->toStdClass($resource)),
                                          $query);

        return $this->resourceFactory->instantiate($returnType, array($response, $query));
    }

    private function applyDefaultRequestHeaders(Request $request)
    {
        $headers = $request->getHeaders();
        $headers['Accept'] = 'application/json';
        $headers['User-Agent'] = 'Stormpath-PhpSDK/' .Version::SDK_VERSION;

        if ($request->getBody())
        {
            $headers['Content-Type'] = 'application/json';
        }

        $request->setHeaders($headers);
    }

    private function toStdClass(Resource $resource)
    {
        $propertyNames = $resource->getPropertyNames();

        $properties = new \stdClass();

        foreach($propertyNames as $name)
        {
            $property = $resource->getProperty($name);

            if ($property instanceof \stdClass)
            {
                $property = $this->toSimpleReference($name, $property);
            }

            $properties->$name = $property;
        }

        return $properties;
    }

    private function toSimpleReference($propertyName, \stdClass $properties)
    {
        $hrefPropName = Resource::HREF_PROP_NAME;

        if (!isset($properties->$hrefPropName))
        {
            throw new \InvalidArgumentException("Nested resource '#{$propertyName}' must have an 'href' property.");
        }

        $href = $properties->$hrefPropName;

        $toReturn = new \stdClass();

        $toReturn->$hrefPropName = $href;

        return $toReturn;
    }

    private function getQueryString(array $options) {

        $query = array();

        // All of the supported options are query strings right now,
        // so we just return the same array with the values converted
        // to string.
        foreach ($options as $key => $opt) {

           $query[$key] = !is_bool($opt)? //only support a boolean or an object that has a __toString implementation
                          strval($opt) :
                          var_export($opt, true);

        }

        return $query;
    }
}
