<?php

/* *
 *  @author Norman Ovenseri <novenseri@gmail.com>
 *  @copyright eMagid 2014
 */

namespace Emagid\Mvc;

/**
 * Renderables output visuals
 * @package Views
 * @category Renderable
 * @abstract
 */
abstract class Renderable
{

	/**
	* @var Array parameters passed to the constructor 
	*/
	var $_params; 


	public function __construct(){

		$this->_params = func_get_args();

	}
  
  public function __toString()
  {
    return (string)$this->_render();
  }
  
  abstract protected function _render();
}
