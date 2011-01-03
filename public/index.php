<?php
session_start();

require_once("../miniMVC.php");

if(DEBUG){
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
}else{
	error_reporting(0);
}

miniMVC::$debug = true;
$app = miniMVC::getInstance();

$app->setUrls(array(
	'^/$'=>'/admin/hello/',
	'^/ecco/([0-9]*)$' => 'test/add/$1',
	'^/remove' => 'test/remove'
));
$app->setControllerDirectory('controllers');

try {
	if(!$app->route($_SERVER['REQUEST_URI'])){	
		echo Controller::_render('home',array('title'=>_('test')));
	}
} catch (Exception $e){
	echo $e->getMessage();
}
