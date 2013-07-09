<?php
/**
 * A class to fetch the Tenant resources
 *
 */

namespace Stormpath\Resource;

use Stormpath\Collection;
use Zend\Http\Client;
use Zend\Json\Json;

class Tenant extends AbstractResource
{
    protected $_url = 'https://api.stormpath.com/v1/tenants';

    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    protected $key;

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($value)
    {
        $this->key = $value;
        return $this;
    }

    protected $applications;

    public function getApplications()
    {
        return $this->applications;
    }

    public function setApplications(Collection $applications)
    {
        $this->applications = $applications;
    }

    protected $directories;

    public function getDirectories()
    {
        return $this->directories;
    }

    public function setDirectories(Collection $directories)
    {
        $this->directories = $directories;
    }

    public function exchangeArray($data)
    {
        $this->setHref(isset($data['href']) ? $data['href']: null);
        $this->setName(isset($data['name']) ? $data['name']: null);
        $this->setKey(isset($data['key']) ? $data['key']: null);

        $this->setApplications(new Collection($this->getResourceManager(), 'Stormpath\Resource\Application', $data['applications']['href']));
        $this->setDirectories(new Collection($this->getResourceManager(), 'Stormpath\Resource\Directory', $data['directories']['href']));
    }

    public function getArrayCopy()
    {
        return array(
            'href' => $this->getHref(),
            'name' => $this->getName(),
            'key' => $this->getKey(),
        );
    }
}