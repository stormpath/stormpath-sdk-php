<?php

namespace Stormpath\Client;

class Proxy
{
	private  $host;
	private  $port;
	private  $username;
	private  $password;
	private  $authenticationRequired;

	
	public function __construct($host, $port, $username = '', $password = '', $authenticationRequired = false)
	{
		if ($host == null) {
			throw new InvalidArgumentException('host argument cannot be null');
		}
			
		if ($port < 0 || $port > 65535) {
			throw new InvalidArgumentException("port must be be between 0 and 65535");
		}

		if ($username != '' && $password != '') {
			$authenticationRequired = true;
		}

			$this->host = $host;
			$this->port = $port;
			$this->username = $username;
			$this->password = $password;
			$this->authenticationRequired = $authenticationRequired;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getPort()
	{
		return $this->port ;
	}

	public function getUserName()
	{
		return $this->username;
	}

	public function getPassword() 
	{
        return $this->password;
    }

    public function isAuthenticationRequired() 
    {
        return $this->authenticationRequired;
    }

    public function buildString()
    {
    	$stringBuilder =  "host=".$this->host."port=".$this->port;
    	if($this->username){
    		$stringBuilder .= "username=".$this->username;
    	}
    	if($this->password){
    		$stringBuilder .= "password<hidden>";
    	}

    	return $stringBuilder;
    }

    public function hashCode()
    {
    	$prime = 31;
    	$result = 1;
    	$result = $prime * $result + ($this->host != null ? spl_object_hash($this->host):0);
    	$result = $prime * $result + ($this->password != null ? spl_object_hash($this->password) : 0);
        $result = $prime * $result + $this->port;
        $result = $prime * $result + ($this->username != null ? spl_object_hash($this->username) : 0);
        $result = $prime * $result + ($this->authenticationRequired ? 1231 : 1237);
        
        return $result;
    }

    public function equals($object)
    {
    	if($this == $object){   
    		return true;
    	}

    	if($object instanceof Proxy){
    		$p = $object;

    		return ($this->host != null ? $this->host->equals($p->getHost()) : $p->getHost() == null) &&
    		($this->port == $p->getPort()) &&
    		($this->username != null ? $this->username->equals($p->getUsername()) : $p->getUsername() == null) &&
            ($this->password != null ? $this->password->equals($p->getPassword()) : $p->getPassword() == null) &&
            ($this->authenticationRequired == $p->$this->authenticationRequired);
    	}

    	return false;
    }

}