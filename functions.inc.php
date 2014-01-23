<?php
/** 
* Global functions 
*/ 


function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}


function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

/** 
	* convert an array to an object 
	*
	* @param Array
	* @return strClass 
	*/
	function array_to_object($array) {
	  $obj = new stdClass;

	  foreach($array as $k => $v) {
	     if(is_array($v)) {
	        $obj->{$k} = $this->array_to_object($v); //RECURSION
	     } else {
	        $obj->{$k} = $v;
	     }
	  }

	  return $obj;
	} 

	/** 
	* convert object to an array
	*
	* @param $data Object 
	* @return Array named array 
	*/
	function object_to_array($data)
	{
	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] = $this->object_to_array($value);
	        }
	        return $result;
	    }
	    return $data;
	}


	/**
	* checks whether an array is associative 
	*/
	function is_assoc($var) {
        return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
	}

?>