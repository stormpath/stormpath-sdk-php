<?php

namespace Stormpath\Resource;

use Stormpath\Resource\AbstractResource;
use Stormpath\Service\StormpathService;
use Stormpath\Collections\ResourceCollection;
use Zend\Http\Client;
use Zend\Json\Json;

class Group extends AbstractResource
{
    protected $_url = 'https://api.stormpath.com/v1/groups';

    protected $name;
    protected $description;
    protected $status;

    protected $tenant;

    protected $directory;
    protected $groups;

    public function getName()
    {
        $this->_load();
        return $this->name;
    }

    public function setName($value)
    {
        $this->_load();
        $this->name = $value;
        return $this;
    }

    public function getDescription()
    {
        $this->_load();
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->_load();
        $this->description = $value;
        return $this;
    }

    public function getStatus()
    {
        $this->_load();
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->_load();
        $this->status = $value;
        return $this;
    }

    public function setTenant(Tenant $value)
    {
        $this->_load();
        $this->tenant = $value;
        return $this;
    }

    public function getTenant()
    {
        $this->_load();
        return $this->tenant;
    }

    public function setDirectory(Directory $value)
    {
        $this->_load();
        $this->directory = $value;
        return $this;
    }

    public function getDirectory()
    {
        $this->_load();
        return $this->directory;
    }

    public function setGroups(ResourceCollection $value)
    {
        $this->_load();
        $this->groups = $value;
        return $this;
    }

    public function getGroups()
    {
        $this->_load();
        return $this->groups;
    }

    public function exchangeArray($data)
    {
        $this->setHref(isset($data['href']) ? $data['href']: null);
        $this->setName(isset($data['name']) ? $data['name']: null);
        $this->setDescription(isset($data['description']) ? $data['description']: null);
        $this->setStatus(isset($data['status']) ? $data['status']: null);

        $tenant = new \Stormpath\Resource\Tenant;
        $tenant->_lazy($this->getResourceManager(), substr($data['tenant']['href'], strrpos($data['tenant']['href'], '/') + 1));
        $this->setTenant($tenant);

        $directory = new \Stormpath\Resource\Directory;
        $directory->_lazy($this->getResourceManager(), substr($data['directory']['href'], strrpos($data['directory']['href'], '/') + 1));
        $this->setDirectory($directory);

        $this->setGroups(new ResourceCollection($this->getResourceManager(), 'Stormpath\Resource\Group', $data['groups']['href']));
    }

    public function getArrayCopy()
    {
        $this->_load();

        return array(
            'href' => $this->getHref(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'status' => $this->getStatus(),
        );
    }
}
