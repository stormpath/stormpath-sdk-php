<?php


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
            $this->baseUrl = 'https://' .= self::DEFAULT_SERVER_HOST . "/v" . self::DEFAULT_API_VERSION;

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
            $$propertiesArr[0] = $properties;
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
        // TODO: Implement create() method.
    }

    public function save(Services_Stormpath_Resource_Resource $resource,
                         $returnType = null)
    {
        // TODO: Implement save() method.
    }

    public function delete(Services_Stormpath_Resource_Resource $resource)
    {
        $this->executeRequest(Services_Stormpath_Http_Request::METHOD_DELETE, $resource->getHref());
    }

    protected function needsToBeFullyQualified($href)
    {
        return stripos($href, 'http') == 0;
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
        $request = new Services_Stormpath_Http_DefaultRequest($httpMethod, $href, $body, strlen($body));

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

    private function applyDefaultRequestHeaders(Services_Stormpath_Http_Request $request)
    {
        $request->getHeaders()['Accept'] = 'application/json';
        $request->getHeaders()['User-Agent'] = 'Stormpath-PhpSDK/' .Services_Stormpath_Version::SDK_VERSION;

        if ($request->getBody())
        {
            $request->getHeaders()['Content-Type'] = 'application/json';
        }
    }
}
