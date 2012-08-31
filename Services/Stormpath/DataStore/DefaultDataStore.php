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
class Services_Stormpath_DataStore_DefaultDataStore
    implements Services_Stormpath_DataStore_InternalDataStore
{

    private $requestExecutor;
    private $resourceFactory;
    private $baseUrl;

    const DEFAULT_SERVER_HOST = 'api.stormpath.com';
    const DEFAULT_API_VERSION = '1';

    public function __construct(Services_Stormpath_Http_RequestExecutor $requestExecutor,
                                $baseUrl = null)
    {
        $this->requestExecutor = $requestExecutor;
        $this->resourceFactory = new Services_Stormpath_DataStore_DefaultResourceFactory($this);

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
     * This method effectively replaces the {@code new} keyword that would have been used otherwise if the concrete
     * implementation was known (Resource implementation classes are intentionally not exposed to SDK end-users).
     *
     * @param $className the Resource class name (as a String) to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     * @param $properties the optional Properties of the Resource to instantiate.
     *
     * @return a new instance of the specified Resource.
     */
    public function instantiate($className, stdClass $properties = null)
    {
        $propertiesArr = array();

        if ($properties)
        {
            $propertiesArr[0] = $properties;
        }

       return $this->resourceFactory->instantiate($className, $propertiesArr);
    }

    /**
     * Looks up (retrieves) the resource at the specified {@code href} URL and returns the resource as an instance
     * of the specified {@code class} name.
     * <p/>
     * The {@code $className} argument must represent the name of an interface that is a sub-interface of
     * {@code Resource}, for example {@code 'Services_Stormpath_Resource_Account'},
     * {@code 'Services_Stormpath_Resource_Directory'}, etc.
     *
     * @param href  the resource URL of the resource to retrieve
     * @param class the {@code Resource} sub-interface to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     * @return an instance of the specified class based on the data returned from the specified {@code href} URL.
     */
    public function  getResource($href, $className)
    {
        if ($this->needsToBeFullyQualified($href))
        {
            $href = $this->qualify($href);
        }

        $data = $this->executeRequest(Services_Stormpath_Http_Request::METHOD_GET, $href);

        return $this->resourceFactory->instantiate($className, array($data));
    }

    public function create($parentHref,
                           Services_Stormpath_Resource_Resource $resource,
                           $returnType)
    {
        $returnedResource = $this->saveResource($parentHref, $resource, $returnType);

        $returnTypeClass = $this->resourceFactory->instantiate($returnType, array());
        if ($resource instanceof $returnTypeClass)
        {
            //ensure the caller's argument is updated with what is returned from the server:
            $resource->setProperties($this->toStdClass($returnedResource));
        }

        return $returnedResource;
    }

    public function save(Services_Stormpath_Resource_Resource $resource,
                         $returnType = null)
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

    public function delete(Services_Stormpath_Resource_Resource $resource)
    {
        return $this->executeRequest(Services_Stormpath_Http_Request::METHOD_DELETE, $resource->getHref());
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

    private function executeRequest($httpMethod, $href, $body = '')
    {
        $request = new Services_Stormpath_Http_DefaultRequest(
                       $httpMethod,
                       $href,
                       array(),
                       array(),
                       $body,
                       strlen($body));

        $this->applyDefaultRequestHeaders($request);

        $response = $this->requestExecutor->executeRequest($request);

        $result = $response->getBody() ? json_decode($response->getBody()) : '';

        if ($response->isError())
        {
            $error = new Services_Stormpath_Resource_Error($result);
            throw new Services_Stormpath_Resource_ResourceError($error);
        }

        return $result;

    }

    private function saveResource($href,
                                  Services_Stormpath_Resource_Resource $resource,
                                  $returnType)
    {
        if ($this->needsToBeFullyQualified($href))
        {
            $href = $this->qualify($href);
        }

        $response = $this->executeRequest(Services_Stormpath_Http_Request::METHOD_POST,
                                          $href,
                                          json_encode($this->toStdClass($resource)));

        return $this->resourceFactory->instantiate($returnType, array($response));
    }

    private function applyDefaultRequestHeaders(Services_Stormpath_Http_Request $request)
    {
        $headers = $request->getHeaders();
        $headers['Accept'] = 'application/json';
        $headers['User-Agent'] = 'Stormpath-PhpSDK/' .Services_Stormpath_Util_Version::SDK_VERSION;

        if ($request->getBody())
        {
            $headers['Content-Type'] = 'application/json';
        }

        $request->setHeaders($headers);
    }

    private function toStdClass(Services_Stormpath_Resource_Resource $resource)
    {
        $propertyNames = $resource->getPropertyNames();

        $properties = new stdClass();

        foreach($propertyNames as $name)
        {
            $property = $resource->getProperty($name);

            if ($property instanceof stdClass)
            {
                $property = $this->toSimpleReference($name, $property);
            }

            $properties->$name = $property;
        }

        return $properties;
    }

    private function toSimpleReference($propertyName, stdClass $properties)
    {
        $hrefPropName = Services_Stormpath_Resource_Resource::HREF_PROP_NAME;

        if (!isset($properties->$hrefPropName))
        {
            throw new InvalidArgumentException ("Nested resource '$propertyName' must have an 'href' property.");
        }

        $href = $properties->$hrefPropName;

        $toReturn = new stdClass();

        $toReturn->$hrefPropName = $href;

        return $toReturn;
    }
}
