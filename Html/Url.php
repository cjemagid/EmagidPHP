<?php 

namespace Emagid\Html ;



/** 
* A helper class for all url related
*/
class Url {

	/**
	* Returns a URL parsed from the routing table .
	*/
	public static function action($name , $params = []){
		$route = self::findRoute($name);

		if ($route){
			$pattern = $route['pattern']; 

			return self::buildUri($pattern, $params);

		}

		return null;

	}


	private static function buildUri($pattern  , $params){

		return "test";

	}

	private static function findRoute($name){
		if (!$name)
		{
			$route = \Emagid\Mvc\Mvc::$base_routes[0];


		}else {
			
			$route = array_filter(\Emagid\Mvc\Mvc::$routes, function($item) use ($name) {
				return $name == $item['name'];
			}) ;

			if ($route && count($route))
				return $route[0];
		}
	}
}

?>