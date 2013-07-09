<?php

namespace Stormpath\Resource;

use Stormpath\ResourceManager;

abstract class AbstractResource
{
    protected $_url = '';

    protected $resourceManager;

    private $__isInitialized__ = true;
    private $_identifier;

    public function getResourceManager()
    {
        return $this->resourceManager;
    }

    public function setResourceManager(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
        return $this;
    }


    public function _lazy($resourceManager, $identifier)
    {
        $__isInitialized__ = false;
        $this->setResourceManager($resourceManager);
        $this->_identifier = $identifier;
    }

    public function _load()
    {
        $__isInitialized__ = true;

        if (!$this->_identifier) {
            return;
        }

        $this->_resourceManager->load($this->_identifier, $this);
        $this->setId($this->_identifier);

        unset($this->_entityPersister, $this->_identifier);
    }

    public function getId()
    {
        return $this->id;
    }

    private function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    public function getHref()
    {
        if ($this->href) {
            return $this->href;
        }

        if ($this->getId()) {
            return $this->url . '/' . $this->getId();
        }
    }

    public function setHref($value)
    {
        $this->href = $value;
        return $this;
    }

    public function _getUrl()
    {
        return $this->_url;
    }

    abstract public function exchangeArray($values);

    abstract public function getArrayCopy();
}