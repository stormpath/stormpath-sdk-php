<?php

namespace Stormpath\Resource;

class Expansion
{
    private $query;
    private $properties;
    
    public function __construct(array $names = array())
    {
    	foreach($names as $name) {
    		$this->addProperty($name);
    	}
    }
    
    /**
     * Add expansion properties to the query
     *
     * @param string $name
     * @param integer $offset
     * @param integer $limit
     * @return object
     */
    public function addProperty($name, $offset = null, $limit = null)
    {
		if($offset || $limit) {
			$pagination = array();
			if($offset) {
				array_push($pagination, "offset:#" . $offset);
			}
			if($limit) {
				array_push($pagination, "limit:#" . $limit);
			}
			$this->properties[$name] = "#" . name . "(#" . join(',',$pagination) . ")";
		}
		else {
			$this->properties[$name] = $name;
		}
		
		return $this;
    }
    
    /**
     * Generate query string from expansion properties
     *
     * @return array
     */
    public function toQuery()
    {
        if(!empty($this->properties)) {
        	return array("expand" => join(",", $this->properties));
        }
    }
}