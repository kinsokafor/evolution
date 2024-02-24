<?php 

namespace EvoPhp\Api;
/**
 * summary
 */

class stdClass
{
    /**
     * summary
     */
    private $properties = [];

    public function __construct()
    {
        
    }

    public function __set($property, $value) {
    	$this->properties[$property] = $value;
    }

    /**
     * getting the property of the api
     */
    public function __get($property) {
    	return $this->properties[$property] ?? NULL;
    }
}