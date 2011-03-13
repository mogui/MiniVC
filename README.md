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
