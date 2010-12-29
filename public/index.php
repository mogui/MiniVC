<?php
session_start();

require_once("../bootstrap.php");

if(DEBUG){
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
}else{
	error_reporting(0);
}
try {
	if(!miniMVC::route($_SERVER['REQUEST_URI'])){	
		echo miniMVC::_render('home',array('title'=>_('test')));
	}
} catch (Exception $e){
	echo $e->getMessage();
}
