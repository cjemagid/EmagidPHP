<?php

if($_SERVER['DOCUMENT_ROOT'])
	define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR  . ROOT_ADD);


require_once("functions.inc.php");
require_once("hooks/__autoloader.php");

spl_autoload_register(['\Emagid\AutoLoader', 'loadNamespace']);

require_once("_emagid.php");



