<?php

namespace Stormpath;

use Zend\Http\Client;
use Zend\Http\Response;

class ResourceManager
{
    private $httpClient;

    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function setHttpClient(Client $value)
    {
        # $client->setOptions(array('sslverifypeer' => false));

        $this->httpClient = $value;
        return $this;
    }

    public function getCurrentTenant()
    {
        $client = $this->getHttpClient();
        $client->setUri('https://api.stormpath.com/v1/tenants/current');
        $client->setMethod('GET');

        $body = $this->validateResponse($client->send());

        $tenant = new Resource\Tenant();
        $tenant->setResourceManager($this);
        $tenant->exchangeArray(json_decode($body, true));

        $this->persist($tenant);

        return $tenant;
    }

    public function validateResponse(Response $response)
    {

        return $response->getBody();
    }

    public function find($className, $id) {
        $resource = new $className();
        $resource->_lazy($this, $id);

        // Because this function can be called arbitrarily load the resource
        // to verify it exists
        $resource->_load();

        $this->persist($resource);

        return $resource;
    }

    public function load($id, $class) {
        // Fetches a GET and hydrates a class
        $client = $this->getHttpClient();
        $client->setUri($class->_getUrl() . '/' . $id);
        $client->setMethod('GET');

        $response = $client->send();

        print_r($response);die();

#        return Json::decode($client->send()->getBody());

    }

    function persist($object)
    {

    }

    /**
     * Removes an object instance.
     *
     * A removed object will be removed from the database as a result of the flush operation.
     *
     * @param object $object The object instance to remove.
     */
    function remove($object)
    {

    }

    /**
     * Merges the state of a detached object into the persistence context
     * of this ObjectManager and returns the managed copy of the object.
     * The object passed to merge will not become associated/managed with this ObjectManager.
     *
     * @param object $object
     * @return object
     */
    function merge($object)
    {

    }

    /**
     * Clears the ObjectManager. All objects that are currently managed
     * by this ObjectManager become detached.
     *
     * @param string $objectName if given, only objects of this type will get detached
     */
    function clear($objectName = null)
    {

    }

    /**
     * Detaches an object from the ObjectManager, causing a managed object to
     * become detached. Unflushed changes made to the object if any
     * (including removal of the object), will not be synchronized to the database.
     * Objects which previously referenced the detached object will continue to
     * reference it.
     *
     * @param object $object The object to detach.
     */
    function detach($object)
    {

    }

    /**
     * Refreshes the persistent state of an object from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param object $object The object to refresh.
     */
    function refresh($object)
    {

    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     */
    function flush()
    {

    }

    /**
     * Gets the repository for a class.
     *
     * @param string $className
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    function getRepository($className)
    {

    }

    /**
     * Returns the ClassMetadata descriptor for a class.
     *
     * The class name must be the fully-qualified class name without a leading backslash
     * (as it is returned by get_class($obj)).
     *
     * @param string $className
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    function getClassMetadata($className)
    {

    }

    /**
     * Gets the metadata factory used to gather the metadata of classes.
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory
     */
    function getMetadataFactory()
    {

    }

    /**
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * This method is a no-op for other objects.
     *
     * @param object $obj
     */
    function initializeObject($obj)
    {

    }

    /**
     * Check if the object is part of the current UnitOfWork and therefore
     * managed.
     *
     * @param object $object
     * @return bool
     */
    function contains($object)
    {

    }
}