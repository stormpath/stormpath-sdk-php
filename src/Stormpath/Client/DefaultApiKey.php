<?php
/**
 *
 */

namespace Stormpath\Client;

class DefaultApiKey
{
    private $id;
    private $secret;

    public function __construct($id, $secret)
    {
        if ($id == null) {
            throw new \InvalidArgumentException("id cannot be null.");
        }
        if ($secret == null) {
            throw new \InvalidArgumentException("secret cannot be null.");
        }
        $this->id = $id;
        $this->secret=$secret;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function toString()
    {
        return strval($this->getId());
    }

    public function hashCode()
    {
        return ($this->id != null) ? (int) spl_object_hash($this->id):0;
    }

    public function equals($o)
    {
        if ($o == $this) {
            return true;
        }
        if ($o instanceof DefaultApiKey) {
            return ($this->id !=null ? $this->id->$this->equals($o->id) : $o->id == null ) &&
                   ($this->secret !=null ? $this->secret->$this->equals($o->secret) : $o->secret == null);

        }
        return false;
    }
}