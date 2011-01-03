<?php
/**
* 
*/
class TestController extends Controller
{
	function add($n){
		$this->render('nuovo',array('title'=>'nuovo'));
	}
	
	function remove(){
		echo 'remove';
	}
}

