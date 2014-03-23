<?php

namespace Emagid\Mvc; 

/**
* Base class for controllers
*/
class Controller{

	/**
	* @var string
	* template to load. Will effect the load_view method
	*/
	public $template = null;

	/**
	* @var string 
	* name of active controller
	*/
	public $name = 'home' ;

	/**
	* @var string 
	* name of active view
	*/
	public $view = 'index';



	public function __construct(){
		global $emagid; 

		if($emagid->template)
			$this->template = $emagid->template;
		
	}


	/**
	* load the view 
	*
	* @param string $view 	name of view to load. default is the class's view
	* @param object $model 	object that contains all the data for the view.
	*         
	*/
	public function loadView(string $view = null , $model = null ){
		global $emagid ; 

		if($view)
			$this->view = $view; 

		if($this->template){

			$path = 'templates/'.$this->template.'/'.$this->template.'.php';
			//$path = (ROOT_PATH.str_replace('/', DIRECTORY_SEPARATOR, $path));


			require($path);
		}else{

			$this->renderBody($model);
		}

	}


	public function renderBody($model = null){
		global $emagid ; 

		$path = 'views'.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR.$this->view.'.php';

		if (file_exists($path)){		
			if(!include($path)){
				die("<h1>Failed to load the view : ".$path."</h1>");
			}
		}
		else {
			echo ("<strong>ERROR : </strong>Could not find file : ".$path);
			//d (\Emagid\Mvc\Mvc::$route);

		}
	}



	/** 
	* Returns an Html renderable object 
	*/ 
	public function view(){
		$html = new \Emagid\Mvc\Views\Html(func_get_args());

		return $html;
	}

	/** 
	* Returns an Html renderable object 
	*/ 
	public function json(){
		$json = new \Emagid\Mvc\Views\Json(func_get_args());

		return $json;
	}

}

?>