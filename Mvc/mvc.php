<?php 
namespace Emagid\Mvc;

use Emagid\Emagid;


/**
* @todo : add routing table support
*/
class Mvc{


	/**
	*
	*/
	public static $errors = [];	



	/**
	* @var string site's root, used to determine where the controller starts 
	*/
	private static $debug = false; 


	/**
	* @var string site's root, used to determine where the controller starts 
	*/
	private static $root = '/'; 


	private static $uri = ''; 


	/**
	* @var string the default controller when none specified
	*/
	private static $default_controller = 'home'; 


	/**
	* @var string the default view when none specified
	*/
	private static $default_view = 'index'; 


	/**
	* @var string the active route .
	*/
	public static $route = [];



	/**
	* @var array - routing table allows the user to  add new "translators " for routes
	* 				- name : name for the route
	*				- pattern : using regular expression
	*				- controller
	*				- action	*/
	private static $routes = []; 

	private static $base_routes = [
			[
			'pattern'=>'{?controller}/{?action}/{?id}',
			'controller' => 'home',
			'action' => 'index'
			]
		]; 


	/**
	* Load the Mvc structure
	* 
	* @param array $arr 
	* 		'root' string - the absolute URI of the current site, should always end with '/' (e.g.: '/', '/mysite/')
	*
	* @todo  Add support for arguments to the routing table.
	*/
	public static function load(array $arr = []){

		global $emagid; 




		$exclude_ext = ['.css' , '.jpg', '.html' ,'.js'];

		if(isset($arr['template']) && $arr['template'])
			$emagid->template=$arr['template'];

		if(isset($arr['root']))
			self::$root=$arr['root'];

		if(isset($arr['default_controller']))
			self::$default_controller=$arr['default_controller'];

		if(isset($arr['default_view']))
			self::$default_view=$arr['default_view'];

		if(isset($arr['routes'])){
			self::$routes=$arr['routes'];
		}


		$uri = $_SERVER['REQUEST_URI'];



		if(stristr($uri, "?")){
			$uri_parts = explode("?", $uri); 
			$uri = $uri_parts[0];

			//$_SERVER['QUERY_STRING'] =$uri_parts[1];
			self::rebuildQueryString($uri_parts[1]);

		}


		if(self::startsWith($uri, self::$root)){
			$uri = substr($uri, strlen(self::$root));
		}

		if(self::startsWith($uri, '/')){
			$uri = substr($uri, 1);
		}

		foreach ($exclude_ext as $ext) {
			if(stristr($uri, $ext)){
				header("HTTP/1.0 404 Not Found");
				die();
			}
		}

		self::$uri = $uri; 



		$route_found = false; 

		$routes = array_merge(self::$routes , self::$base_routes);
		$ok_routes = [];

		foreach ($routes as $route) {
			$in_route = self::testPattern($route);

			if ($in_route){
				$ok_routes[] = $in_route; 

			}	
		}


		$route_found = count($ok_routes);

		if ($route_found ) {
			$route = $ok_routes[0];
			
			self::$route = $route ;

 			$controller_name = $route['controller'] ;
		 	$view_name = $route['action'];
		 	$segments = $route;


		} else {


			$segments = $uri != '' && $uri != '/' ? explode('/', $uri) : array();



			$controller_name = self::getAndPop($segments) ; 


			if(!$controller_name ) {
					// if controller doesn't exist, view won't exist neigther .
					$controller_name = self::$default_controller ;
					$view_name = self::$default_view;
			}else {
				// controller exists, might have view definition, and parameters .
				$view_name = self::getAndPop($segments);

				if(!$view_name)
					$view_name = self::$default_view;
				
			}
		}






		/*$controller_name = self::loadController($controller_name); 

		if (!$controller_name)
			return ;*/

		$name = $controller_name;
		$controller_name .= 'Controller'; 

		if (class_exists($controller_name)){
			$emagid->controller = new $controller_name();
			$emagid->controller->name = $name;
		} else {
			$errors[] = "Controller $name was not found";
			// controller was not found, trying to load the view directly 
			$emagid->controller = new \Emagid\Mvc\Controller();
			$emagid->controller->name = $name;

		}


		$emagid->controller->view = $view_name;

		$req = strtolower($_SERVER['REQUEST_METHOD']); 

		$method = $view_name.'_'.$req; 
		

		if(method_exists($emagid->controller, $method)){ 
			call_user_func_array(array(&$emagid->controller, $method),$segments);
		}else if(method_exists($emagid->controller, $view_name)) {
			call_user_func_array(array(&$emagid->controller, $view_name),$segments);
		} else  {
			$emagid->controller->loadView();
		}



		die();
		

	}


	/**
	* Make sure a pattern applies to the current uri . 
	*/
	private static function testPattern($pattern){
		$uri = self::$uri;

		$segments = explode('/', $pattern['pattern']);

		$uri_segments = explode('/', $uri);

		$mvc_parts = array_merge([],$pattern);

		foreach ($segments as $index=>$segment) {

			$psegment = $segments[$index]; 
			$val = ($index >= count($uri_segments))?'':$uri_segments[$index]; 
			

			if (startsWith($psegment,'{') && endsWith($psegment,'}')  ){
				$psegment = trim(substr($psegment,1,strlen($psegment)-2));

				if (startsWith($psegment,'?')){
					$optional = true ; 
					$psegment = substr($psegment, 1) ;
				} else {
					$optional = false;
				}


				if ($val){
					$mvc_parts[$psegment] = $uri_segments[$index];
				} else { // mandatory parameter not supplied

					if (!$optional)
						return false;

				}
				
			} else {

				if ($psegment != $val)
					return false;
			}
		}

		return $mvc_parts;
	}


	/**
	* Get the first element from the array and remove it 
	*
	* @param array &$arr reference to the array 
	*/
	private static function getAndPop(&$arr ){
		if(count($arr)){
			$ret = $arr[0];

			array_shift($arr);

			return $ret ;

		}

		return null; 
	}



	/**
	* Rebuild the querystring 
	*
	* @param string $str - the text that comes after the "?" 
	*/
	private static function rebuildQueryString ($str){
		global $_GET; 

		$_GET = [] ;

		foreach (explode("&", $str) as $qs) {
			$key = explode("=", $qs)[0];
			$val = explode("=", $qs)[1];

			$_GET[$key] = $val;
		}



	}

	/** 
	* Checks whether a strings starts with a specific string.
	*
	* @todo Move this function to functions.inc.php
	*/
	static function startsWith($haystack,$needle,$case=true){
		if($case)
       		return strpos($haystack, $needle, 0) === 0;

   		return stripos($haystack, $needle, 0) === 0;
	}


	/** 
	* Config routes, allowing to override the url when necessary 
	*
	* @param array $routingTable 
	* 				- name : name for the route
	*				- pattern : using regular expression
	*				- controller
	*				- view
	*
	* @todo expand functionality and add support for route variables. 
	*/
	// static function registerRoutes ($routingTable) {
	// 	self::$routes = $routingTable;
	// }


	
}



?>