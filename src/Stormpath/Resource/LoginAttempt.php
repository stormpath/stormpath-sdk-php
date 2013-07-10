<?php

namespace Stormpath\Resource;

use Stormpath\Resource\AbstractResource;
use Stormpath\Service\StormpathService;
use Stormpath\Resource\Account;

class LoginAttempt extends AbstractResource
{
    /**
     * Login attempts cannot be lazy loaded or loaded directly
     */
    protected $_url = '';

    protected $type = 'basic';
    protected $username;
    protected $password;

    protected $application;
    private $account;

    public function setApplication(Application $value)
    {
        $this->application = $value;
        return $this;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setType($value)
    {
        $this->type = $value;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setUsername($value)
    {
        $this->username = $value;
        return $this;
    }

    public function setPassword($value)
    {
        $this->password = $value;
        return $this;
    }

    private function getValue()
    {
        return base64_encode($this->username . ':' . $this->password);
    }

    private function setAccount(Account $value)
    {
        $this->account = $value;
        return $this;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function exchangeArray($data)
    {
        $account = new Account;
        $account->_lazy($this->getResourceManager(), substr($data['account']['href'], strrpos($data['account']['href'], '/') + 1));
        $this->setAccount($account);
    }

    public function getArrayCopy()
    {
        return array(
            'type' => $this->getType(),
            'value' => $this->getValue(),
        );
    }
}
