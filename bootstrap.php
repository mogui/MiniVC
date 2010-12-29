<?php
/**
 * A basic bootstrap for miniMVC
 *
 * this file is included by the index.php here we do all the initial settings for the Framework
 * also here we can include any other library to make it available through the project
 * it is reccomended to have a particular directory structure as follow:
 * 	root/
 *		public/
 *			<JS CSS IMG FOLDER - STATIC CONTENT>
 *		classes/
 *		lib/
 *			<ANY OTHER LIBRARY>
 *		layout/
 *			<TEMPLATE FILES>
 *
 * @author mogui
 * @version 1.0
 * @copyright mogui, 29 December, 2010
 * @package miniMVC
 **/

/**
 * Defining the APP_PATH constant used by other pieces of the FrameWork
 **/

$app_path = dirname(__FILE__);
define('APP_PATH', $app_path);
require_once(APP_PATH .'miniMVC.php');

function __autoload($Class) {
	if(file_exists(APP_PATH . '/classes/'.$Class.'.php')) {
    	include APP_PATH . '/classes/'.$Class.'.php';
    } else {
    	throw new Exception(_('Class not exist.'));
    }
}

?>