<?php

class Proxy
{
	private  $host;
	private  $port;
	private  $userName;
	private  $password;
	private  $authenticationRequired;

	
	public function __construct($host, $port, $username = '', $password = '', $authenticationRequired = false)
	{
		if ($host == null) {
			throw new InvalidArgumentException('host argument cannot be null');
		}
			
		if (port < 0 || port > 65535) {
			throw new InvalidArgumentException("port must be be between 0 and 65535");
		}

		if ($userName != '' && $password != '') {
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
		return $host;
	}

	public function getPort()
	{
		return $port;
	}

	public function getUserName()
	{
		return $username;
	}

	public function getPassword() 
	{
        return $password;
    }

    public function isAuthenticationRequired() 
    {
        return $authenticationRequired;
    }

    public function buildString()
    {
    	$stringBuilder =  "host=".$host."port=".$port;
    	if($username){
    		$stringBuilder .= "username=".$username;
    	}
    	if($password){
    		$stringBuilder .= "password<hidden>";
    	}

    	return $stringBuilder;
    }

    public function hashCode()
    {
    	$prime = 31;
    	$result = 1;
    	$result = $prime * $result + ($host != null ? $host->hashCode():0);
    	$result = $prime * $result + ($password != null ? $password->hashCode() : 0);
        $result = $prime * $result + $port;
        $result = $prime * $result + ($username != null ? $username->hashCode() : 0);
        $result = $prime * $result + ($authenticationRequired ? 1231 : 1237);
        
        return $result;
    }

    public function equals($StdClass $object)
    {
    	if($this == $object){   
    		return true;
    	}

    	if($object instanceof $proxy){
    		Proxy $p = (Proxy)$object;

    		return ($host != null ? $host->equals($p->getHost()) : p->getHost() == null) && 
    		($port == $p->getPort()) && 
    		($username != null ? $username->equals($p->getUsername()) : $p->getUsername() == null) &&
            ($password != null ? $password->equals($p->getPassword()) : $p->getPassword() == null) &&
            ($authenticationRequired == $p->$authenticationRequired);
    	}

    	return false;
    }

}