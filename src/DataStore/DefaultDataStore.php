<?php

namespace Stormpath\DataStore;

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

use Stormpath\ApiKey;
use Stormpath\Cache\Cacheable;
use Stormpath\Http\DefaultRequest;
use Stormpath\Http\Request;
use Stormpath\Http\RequestExecutor;
use Stormpath\Resource\CustomData;
use Stormpath\Resource\Error;
use Stormpath\Resource\Resource;
use Stormpath\Resource\Directory;
use Stormpath\Resource\ResourceError;
use Stormpath\Stormpath;
use Stormpath\Util\UserAgentBuilder;
use Stormpath\Cache\PSR6CacheKeyTrait;
use Cache\Taggable\TaggablePoolInterface;
use Cache\Taggable\TaggableItemInterface;
use Stormpath\Cache\Tags\CacheTagExtractor;

class DefaultDataStore extends Cacheable implements InternalDataStore
{
    use PSR6CacheKeyTrait;

    private $requestExecutor;
    private $resourceFactory;
    private $baseUrl;
    protected $cachePool;

    private $apiKey;

    const DEFAULT_SERVER_HOST = 'api.stormpath.com';
    const DEFAULT_API_VERSION = '1';

    public function __construct(RequestExecutor $requestExecutor, ApiKey $apiKey, TaggablePoolInterface $cachePool, $baseUrl = null)
    {
        $this->requestExecutor = $requestExecutor;
        $this->resourceFactory = new DefaultResourceFactory($this);
        $this->cachePool = $cachePool;

        $this->apiKey = $apiKey;


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

        $resource = $this->resourceFactory->instantiate($className, $propertiesArr);

        return $resource;

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

        $item = $this->cachePool->getItem($this->createCacheKey($href, $options));

        if (!$item->isHit()) {
            $data = $this->executeRequest(Request::METHOD_GET, $href, '', $queryString);

            if ($this->responseIsCacheable($data)) {
                $item->set($data);

                $cacheTags = CacheTagExtractor::extractCacheTags($data);
                $cacheTags = array_map([$this, 'normalizeHrefAsCacheTag'], $cacheTags);

                $item->setTags($cacheTags);

                $this->cachePool->save($item);
            }
        } else {
            $data = $item->get();
        }

        $resolver = DefaultClassNameResolver::getInstance();
        $className = $resolver->resolve($className, $data, $options);

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
            throw new \InvalidArgumentException('save may only be called on objects that have already been persisted (i.e. they have an existing href).');
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
        $delete = $this->executeRequest(Request::METHOD_DELETE, $resource->getHref());
        $this->removeResourceFromCache($resource);
        return $delete;
    }

    public function removeCustomDataItem(Resource $resource, $key)
    {
        $delete = $this->executeRequest(Request::METHOD_DELETE, $resource->getHref().'/'.$key);
        $this->removeResourceFromCache($resource);
        return $delete;
    }

    public function getRequestExecutor()
    {
        return $this->requestExecutor;
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
                       $this->apiKey,
                       $httpMethod,
                       $href,
                       $query,
                       array(),
                       $body,
                       strlen($body));

        $this->applyDefaultRequestHeaders($request);

        $response = $this->requestExecutor->executeRequest($request);

        $result = $response->getBody() ? json_decode($response->getBody()) : null;

        if (isset($result) && $result instanceof \stdClass)
        {
        	$result->httpStatus = $response->getHttpStatus();
        }
        
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
        
        //provider's account creation status (whether it is new or not) is returned in the HTTP response
        //status. The resource factory does not provide a way to pass such information when instantiating a resource. Thus,
        //after the resource has been instantiated we are going to manipulate it before returning it in order to set the
        //"is new" status
        if (isset($response) && isset($response->httpStatus))
        {
        	$httpStatus = $response->httpStatus;
        	if ($returnType == Stormpath::PROVIDER_ACCOUNT_RESULT && ($httpStatus == 200 || $httpStatus == 201))
        	{
        		$response->newAccount = $httpStatus == 201;
        	}
        	unset($response->httpStatus);
        }

        $this->removeHrefFromCache($href);
        if (isset($response->href)) {
            $this->removeHrefFromCache($response->href);
        }

        if($this->responseIsCacheable($response)) {
            $this->addResponseToCache($response, http_build_query($query));
        }

        return $this->resourceFactory->instantiate($returnType, array($response, $query));
    }

    private function applyDefaultRequestHeaders(Request $request)
    {
        $headers = $request->getHeaders();
        $headers['Accept'] = 'application/json';

        $userAgentBuilder = new UserAgentBuilder;
        $headers['User-Agent'] = $userAgentBuilder->setOsName(php_uname('s'))
                                                  ->setOsVersion(php_uname('r'))
                                                  ->setPhpVersion(phpversion())
                                                  ->build();

        if ($body = $request->getBody())
        {
            $headers['Content-Type'] = 'application/json';

            if(strpos($request->getResourceUrl(), '/oauth/token')) {
                $arr = json_decode($body);
                $arr = (array) $arr;
                ksort($arr);
                $body = http_build_query($arr);
                $request->setBody($body, strlen($body));

                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                $headers['Content-Length'] = $request->getContentLength();
            }
        }

        $request->setHeaders($headers);
    }

    private function toStdClass(Resource $resource, $customData = false)
    {
        if($resource instanceof \Stormpath\Resource\CustomData)
        {
            $customData = true;
        }

        $propertyNames = $resource->getPropertyNames(true);

        $properties = new \stdClass();

        foreach($propertyNames as $name)
        {

            $property = $resource->getProperty($name);

            $nameIsCustomData = $name == CustomData::CUSTOMDATA_PROP_NAME;

            if ($property instanceof \Stormpath\Resource\CustomData)
            {
                $property = $this->toStdClass($property, true);
            }

            else if ($property instanceof \stdClass && $customData === false && !$nameIsCustomData)
            {
                $property = $this->toSimpleReference($name, $property);
            }

            else if ($property instanceof \Stormpath\Resource\Resource)
            {
                $property = $this->toStdClass($property);
            }

            $properties->$name = $property;
        }

        return $properties;
    }

    private function objectArrayToStdClass($property)
    {
        $properties = new \stdClass();

        $class = new \ReflectionClass($property);

        $classProperties = $class->getProperties();

        foreach($classProperties as $prop) {
            $method = 'get'.ucfirst($prop->name);
            if(method_exists($property, $method)) {

                $properties->{$prop->name} = $property->$method();
            }
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

    /** This method is not for use by enduser.
     *  The method will be removed without warning
     *  at a future time.  */
    public function getCachePool() {
        return $this->cachePool;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    protected function responseIsCacheable($response)
    {
        return $this->resourceIsCacheable($response);
    }

    protected function removeResourceFromCache($resource)
    {
        switch (true) {
            case $resource instanceof Directory:
                $this->cachePool->clear();
                break;
            default:
                $this->removeHrefFromCache($resource->getHref());
        }
    }

    protected function removeHrefFromCache($href)
    {
        $this->cachePool->deleteItem($this->createCacheKey($href));

        $this->cachePool->clearTags([$this->normalizeHrefAsCacheTag($href)]);
    }

    protected function addResponseToCache(\stdClass $response, $query, array $options = [], TaggableItemInterface $item = null)
    {
        $href = $response->href;
        if ($query) {
            $href .= '?'.$query;
        }

        if (!$item) {
            $item = $this->cachePool->getItem($this->createCacheKey($href, $options));
        }

        if($this->responseIsCacheable($response, $options)) {
            $item->set($response);

            $cacheTags = CacheTagExtractor::extractCacheTags($response);
            $cacheTags = array_map([$this, 'normalizeHrefAsCacheTag'], $cacheTags);

            $item->setTags($cacheTags);

            $this->cachePool->save($item);
        }
    }

    protected function normalizeHrefAsCacheTag($tag)
    {
        return $this->createCacheKey($tag);
    }
}
