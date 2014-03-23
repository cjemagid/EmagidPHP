<?php 

namespace Emagid\Html ;



/** 
* A helper class for all url related
* @todo : need to rethink the inheritance .
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


	/**
	* Build a URI from an MVC pattern and input parameters.
	*/
	private static function buildUri($pattern  , $params){
		$parts = explode('/', $pattern);

		$uri_parts = [] ;



		foreach ($parts as $part_in) {

			$part = \Emagid\Mvc\Mvc::analyzePatternPart($part_in);

			if (!$part->static){

				if (isset($params[$part->name])){
					$val = trim($params[$part->name]);

					if ($part->name == 'controller' and endsWith($val,'Controller') ){
						$part->value = substr($val, 0, strlen($val) - strlen('Controller'));
					} else {
						$part->value = $val;
					}
					
					// removing the item from the array, so we can use that array later for querystring
					unset($params[$part->name]);

				}elseif ($part->required){
					throw new Exception('Mandatory URI part : <{$part->name}> is missing');
				}

				
			}

			if ( $part->value){
				$uri_parts[] = $part->value ;
			}
			
		}

		$uri = \Emagid\Mvc\Mvc::$root . implode('/', $uri_parts);


		if ($params && count($params)){
			$uri .= "?" . http_build_query($params);

		}
		
		

		return $uri;

	}


	/**
	* Located a defined route by its name 
	* @param string $name - name of route, empty will return the defualt route .
	*/
	private static function findRoute($name){
		if (!$name)
		{
			$route = \Emagid\Mvc\Mvc::$base_routes[0];

			return $route;

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