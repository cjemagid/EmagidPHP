<?php 

namespace Emagid\Html ;


/** 
* A helper class for all Html related
*/
class Html {

	/**
	* Returns an 'a' tag for a specific MVC route .
	*/
	public static function actionLink($contnet , $name, $routingParams = [], $htmlParams = []){
		$htmlParams['href'] = Url::action($name, $routingParams);

		return self::buildTag($contnet, 'a', $htmlParams);

	}



	private static function buildTag($content , $tagName , $htmlParams){
		$attributes = '' ;

		if ($htmlParams && count($htmlParams)){
			array_walk($htmlParams, function ($v, $k) use (&$attributes) {
				$attributes.=sprintf("%s=\"%s\"", $k, $v);
			} );
		}


		return sprintf("<%s %s>%s</%s>", $tagName, $attributes, $content, $tagName);
	}
}

?>