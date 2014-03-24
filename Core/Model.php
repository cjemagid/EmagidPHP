<?php 

namespace Emagid\Core ; 



abstract class Model {

	public function __call($name, $arguments)
    {
    	dd();
    }

    
    public static function __callStatic($name, $arguments)
    {
    	$class = get_called_class(); 

    	//$obj = new $class() ;

		$refClass = new \ReflectionClass($class);
		$object = $refClass->newInstanceArgs($arguments);

		return $object; 
        //return $obj->$name($arguments);
    }

}