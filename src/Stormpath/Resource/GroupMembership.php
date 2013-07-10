<?php

namespace Stormpath\Resource;

use Stormpath\Resource\AbstractResource;
use Stormpath\Resource\Group;
use Stormpath\Resource\Account;
use Stormpath\Service\StormpathService;
use Stormpath\Collections\ResourceCollection;
use Zend\Http\Client;
use Zend\Json\Json;

class GroupMembership extends AbstractResource
{
    protected $_url = 'https://api.stormpath.com/v1/groupMemberships';

    protected $account;
    protected $group;

    public function setAccount(Account $value)
    {
        $this->_load();
        $this->account = $value;
        return $this;
    }

    public function getAccount()
    {
        $this->_load();
        return $this->account;
    }

    public function setGroup(Group $value)
    {
        $this->_load();
        $this->group = $value;
        return $this;
    }

    public function getGroup()
    {
        $this->_load();
        return $this->group;
    }

    public function exchangeArray($data)
    {
        $this->setHref(isset($data['href']) ? $data['href']: null);

        $account = new Account;
        $account->_lazy($this->getResourceManager(), substr($data['account']['href'], strrpos($data['account']['href'], '/') + 1));
        $this->setAccount($account);

        $group = new Group;
        $group->_lazy($this->getResourceManager(), substr($data['group']['href'], strrpos($data['group']['href'], '/') + 1));
        $this->setGroup($tenant);
    }

    public function getArrayCopy()
    {
        $this->_load();

        return array(
            'href' => $this->getHref(),
        );
    }
}
