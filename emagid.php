<?php


if($_SERVER['DOCUMENT_ROOT'])  {
	$folder = dirname(dirname(__DIR__));  // assuming that the library is in '/lib/emagid/'

	set_include_path($folder);

	define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] .  (defined(ROOT_ADD) ? DIRECTORY_SEPARATOR.ROOT_ADD : ''));
}



require_once("includes".DIRECTORY_SEPARATOR."functions.inc.php");

require_once("hooks/__autoloader.php");

spl_autoload_register(['\Emagid\AutoLoader', 'loadNamespace']);

require_once("_emagid.php");



