<?php
/** 
* Global functions 
*/ 



/**
* Clone all the fields from one object into another, both already initialized
*/
function clone_into($source, &$target){

	  foreach($source as $k => $v) {
	     if(is_array($v)) {
	        $target->{$k} = array_to_object($v); //RECURSION
	     } else {
	        $target->{$k} = $v;
	     }
	  }

}


/**
* Simple redirect
*
* @param string $url 
*/
function redirect($url){
	header("Location:".$url);
	die(); 
}




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

  return json_decode(json_encode($array));
  
  // $obj = new stdClass;

  // foreach($array as $k => $v) {
  //    if(is_array($v)) {
  //       $obj->{$k} = $this->array_to_object($v); //RECURSION
  //    } else {
  //       $obj->{$k} = $v;
  //    }
  // }

  // return $obj;
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
            
            $result[$key] = object_to_array($value);
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
