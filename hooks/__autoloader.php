<?php

namespace Emagid; 



class AutoLoader {
	/** 
	* Load classes / models on demand 
	*/ 
	public static function  loadNamespace($classname){
		global $included_folders;

		$filename = "lib".DIRECTORY_SEPARATOR .$classname.".php";

		if (file_exists($filename)){
			include($filename);

			if (class_exists($classname)) return true;
		}else{
		}


		return false;
		



		// $paths = [
		// 	ROOT_PATH. DIRECTORY_SEPARATOR. $classname.'.php',
		// 	dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.$classname.'.php'

		// ];


		// $classname = str_replace('\\', '/', $classname);
		// $classname = str_replace('/', DIRECTORY_SEPARATOR, $classname);

		// if(stristr($classname, DIRECTORY_SEPARATOR)){

		// 	$index = 0 ;
		// 	$segments  = explode(DIRECTORY_SEPARATOR, $classname); 

		// 	while(count($segments)>0){ 
		// 		unset($segments[0]);
		// 		$segments = array_values($segments);
		
		// 		$path = implode(DIRECTORY_SEPARATOR, $segments); 
		// 		$paths[] = ROOT_PATH. DIRECTORY_SEPARATOR. $path .'.php';
		// 		$paths[] = ROOT_PATH. DIRECTORY_SEPARATOR. 'lib'.DIRECTORY_SEPARATOR.$path .'.php';

		// 	}
		// }

		

		// foreach ($paths as $path) {
		// 	if(file_exists($path))
		// 	{
		// 		require_once($path);
		// 		if(class_exists($classname))
		// 		{
		// 			return true ;
		// 		}
		// 	}
		// }

		// return false;


		// if(file_exists($classname.'.php')){
		// 	require_once($classname.'.php');
		// }else 
		// 	die($classname);

		// if (stristr($classname ,'\\')){
		// 	$classname = str_replace('\\', '/', $classname);
		// }


		// if (stristr($classname ,'/')){
		// 	$segments = explode('/', $classname);
		// 	$filename = $segments[count($segments)-1];
		// } else {
		// 	$filename = $classname; 
		// }

		// require_once(__DIR__.'/lib/Model/'.$filename.'.php');
		
	}


	/**
	* test path to include 
	*/ 
	function __em_test_path ($path , $classname ) {

	}


}


