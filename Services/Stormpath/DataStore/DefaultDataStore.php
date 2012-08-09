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
                                String $baseUrl = null)
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
     * @param class the Resource class name (as a String) to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     *
     * @return a new instance of the specified Resource.
     */
    public function instantiate($class)
    {
        // TODO: Implement instantiate() method.
    }

    /**
     * Looks up (retrieves) the resource at the specified {@code href} URL and returns the resource as an instance
     * of the specified {@code class} name.
     * <p/>
     * The {@code class} argument must represent an interface that is a sub-interface of
     * {@code Resource}, for example {@code 'Services_Stormpath_Resource_Account'},
     * {@code 'Services_Stormpath_Resource_Directory'}, etc.
     *
     * @param href  the resource URL of the resource to retrieve
     * @param class the {@code Resource} sub-interface to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     * @return an instance of the specified class based on the data returned from the specified {@code href} URL.
     */
    public function  getResource(String $href, $class)
    {
        // TODO: Implement getResource() method.
    }

    public function instantiateWithProperties($class, array $properties)
    {
        // TODO: Implement instantiateWithProperties() method.
    }

    public function create(String $parentHref,
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
        // TODO: Implement delete() method.
    }

}
