<?php

namespace Stormpath\Persistence;

use Stormpath\Resource;
use Stormpath\Exception\ApiException;
use Zend\Http\Client;
use Zend\Http\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Cache\Storage\StorageInterface;
use Stormpath\Service\StormpathService;

class ResourceManager implements ObjectManager
{
    private $httpClient;
    private $delete;
    private $insert;
    private $update;
    private $cache;
    private $expandReferences;

    public function getCache()
    {
        return $this->cache;
    }

    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function getHttpClient()
    {
        $this->httpClient->resetParameters();
        return $this->httpClient;
    }

    public function setHttpClient(Client $client)
    {
        # $client->setOptions(array('sslverifypeer' => false));

        $this->httpClient = $client;
        return $this;
    }

    public function find($className, $id, $expandReferences = false)
    {
        $this->setExpandReferences($expandReferences);

        $resource = new $className();
        $resource->_lazy($this, $id);

        // Because this function can be called arbitrarily load the resource
        // immediatly and to verify it exists
        $resource->_load();

        return $resource;
    }

    public function getExpandReferences()
    {
        return $this->expandReferences;
    }

    public function setExpandReferences($value)
    {
        $this->expandReferences = $value;
        return $this;
    }

    // Fetches a GET and hydrates a class
    public function load($id, $class)
    {
        $success = false;
        if (!$this->getExpandReferences()) {
            $cachedJson = $this->getCache()->getItem(get_class($class) . $id, $success);
        }

        if ($success) {
            $class->exchangeArray(json_decode($cachedJson, true));
        } else {
            $client = $this->getHttpClient();
            $client->setUri($class->_getUrl() . '/' . $id);
            $client->setMethod('GET');

            if ($this->getExpandReferences()) {
                $client->setParameterGet(array(
                    'expand' => $class->getExpandString(),
                ));

                die('expand set to ' . $class->getExpandString());
            }

            $response = $client->send();
            if ($response->isSuccess()) {
                $class->exchangeArray(json_decode($response->getBody(), true));
                $this->getCache()->setItem(get_class($class) . $id, $response->getBody());
            } else {
                $this->handleInvalidResponse($response);
            }
        }
    }

    /**
     * Handle all non 200 OK responses
     */
    public function handleInvalidResponse(Response $response)
    {
        $details = json_decode($response->getBody(), true);

        $exception = new ApiException($details['message'], $details['code']);
        $exception->exchangeArray($details);

        throw $exception;
    }

    function persist($object)
    {
        if (!$this->insert) {
            $this->insert = new ArrayCollection;
        }

        if (!$this->update) {
            $this->update = new ArrayCollection;
        }

        if ($object->getId()) {
            $this->update->add($object);
        } else {
            $this->insert->add($object);
        }
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
        if (!$this->delete) {
            $this->delete = new ArrayCollection;
        }

        if ($object->getId()) {
            // Objects with no id have not been created
            $this->delete->add($object);
        }
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
        throw new \Exception('Merge not implemented');
    }

    /**
     * Clears the ObjectManager. All objects that are currently managed
     * by this ObjectManager become detached.
     *
     * @param string $objectName if given, only objects of this type will get detached
     */
    function clear($objectName = null)
    {
        $this->insert = new ArrayCollection;
        $this->update = new ArrayCollection;
        $this->delete = new ArrayCollection;
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
        if ($this->insert) {
            if ($this->insert->contains($object)) {
                $this->insert->removeElement($object);
                return;
            }
        }

        if ($this->update) {
            if ($this->update->contains($object)) {
                $this->update->removeElement($object);
                return;
            }
        }

        if ($this->delete) {
            if ($this->delete->contains($object)) {
                $this->delete->removeElement($object);
                return;
            }
        }
    }

    /**
     * Refreshes the persistent state of an object from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param object $object The object to refresh.
     */
    function refresh($object)
    {
        $object->_load($object->getId());
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     */
    function flush()
    {
        if ($this->insert) {
            foreach ($this->insert as $resource) {
                switch(get_class($resource)) {
                    case 'Stormpath\Resource\Account':
                        if ($resource->getDirectory()) {
                            $resource->_setUrl(StormpathService::getBaseUrl() . '/directories/' . $resource->getDirectory()->getId() . '/accounts');
                        } else {
                            $resource->_setUrl(StormpathService::getBaseUrl() . '/applications/' . $resource->getApplication()->getId() . '/accounts');
                        }
                        break;
                    case 'Stormpath\Resource\Group':
                        $resource->_setUrl(StormpathService::getBaseUrl() . '/directories/' . $resource->getDirectory()->getId() . '/groups');
                        break;
                    case 'Stormpath\Resource\LoginAttempt':
                        $resource->_setUrl(StormpathService::getBaseUrl() . '/applications/' . $resource->getApplication()->getId() . '/loginAttempts');
                        break;
                    default:
                        break;
                }
                // Create a resource
                $client = $this->getHttpClient();
                $client->setUri($resource->_getUrl());
                $client->setMethod('POST');

                $client->setRawBody(json_encode($resource->getArrayCopy()));
                $response = $client->send();

                if ($response->isSuccess()) {
                    $resource->setResourceManager($this);
                    $newProperties = json_decode($response->getBody(), true);

                    $resource->exchangeArray($newProperties);
                    $this->getCache()->setItem(get_class($resource) . $resource->getId(), $response->getBody());
                } else {
                    $this->handleInvalidResponse($response);
                }

                $this->insert->removeElement($resource);
            }
        }

        if ($this->update) {
            foreach ($this->update as $resource) {
                $resource->_load();

                // Delete a resource
                $client = $this->getHttpClient();
                $client->setUri($resource->getHref());
                $client->setMethod('POST');

                $client->setRawBody(json_encode($resource->getArrayCopy()));
                $response = $client->send();

                if ($response->isSuccess()) {
                    $resource->exchangeArray(json_decode($response->getBody(), true));
                    $this->getCache()->setItem(get_class($resource) . $resource->getId(), $response->getBody());
                } else {
                    $this->handleInvalidResponse($response);
                }

                $this->update->removeElement($resource);
            }
        }

        if ($this->delete) {
            foreach ($this->delete as $resource) {
                $resource->_load();

                // Delete a resource
                $client = $this->getHttpClient();
                $client->setUri($resource->getHref());
                $client->setMethod('DELETE');

                $response = $client->send();

                if ($response->isSuccess()) {
                } else {
                    $this->handleInvalidResponse($response);
                }

                $this->getCache()->removeItem(get_class($resource) . $resource->getId());
                $this->delete->removeElement($resource);
            }
        }
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