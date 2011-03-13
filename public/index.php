<?php
session_start();

require_once("../miniVC.php");

if(DEBUG){
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
}else{
	error_reporting(0);
}

miniVC::$debug = true;
$app = miniVC::getInstance();

$app->setUrls(array(
	'^/$'=>'/hello/world/',
	'^/extends/([\w]*)/$' => 'hello/extends_world/$1'
));
$app->setControllerDirectory('controllers');

try {
	if(!$app->route($_SERVER['REQUEST_URI'])){	
		echo Controller::_render('home',array('title'=>_('test')));
	}
} catch (Exception $e){
	echo $e->getMessage();
}
