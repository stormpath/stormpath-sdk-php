<?php

namespace Stormpath\Resource;

use Stormpath\Resource\AbstractResource;
use Stormpath\Service\StormpathService;

class LoginAttempt extends AbstractResource
{
    /**
     * Login attempts cannot be lazy loaded or loaded directly
     */
    protected $_url = '';

    protected $type;
    protected $value;

    public function setType($value)
    {
        $this->_load();
        $this->type = $value;
        return $this;
    }

    public function getType()
    {
        $this->_load();
        return $this->type;
    }

    public function setValue($value)
    {
        $this->_load();
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        $this->_load();
        return $this->value;
    }

    public function exchangeArray($data)
    {
        $this->setType(isset($data['type']) ? $data['type']: null);
        $this->setValue(isset($data['value']) ? $data['value']: null);
    }

    public function getArrayCopy()
    {
        $this->_load();

        return array(
            'type' => $this->getType(),
            'value' => $this->getValue(),
        );
    }
}
