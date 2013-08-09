<?php

namespace Emagid\Mvc; 

/**
* Base class for controllers
*/
abstract class Controller{

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
	
	/**
	* @var string 
	* name of active area
	*/
	public $area = '';



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
	protected function loadView(string $view = null , $model = null ){
		global $emagid ; 

		if($view)
			$this->view = $view; 

		if($this->template){
			$path= $emagid->base_path.'/templates/'.$this->template.'/'.$this->template.'.php';
			include($path);
		}else{

			$this->renderBody($model);
		}

	}


	public function renderBody($model = null){
		global $emagid ; 

		$path= $emagid->base_path.'/views/'.$this->area.$this->name.'/'.$this->view.'.php';



		if(!include($path)){
			die("<h1>Failed to load the view : ".$path."</h1>");
		}
	}

}

?>