<?php

namespace Emagid\Mvc\Views;

/**
 * Renderables output visuals
 * @package Views
 * @category Renderable
 * @abstract
 */
class Json extends \Emagid\Mvc\Renderable
{
  
	function _render(){
		header("Content-type: application/json");

		die (json_encode($this->_params));

	}
}
