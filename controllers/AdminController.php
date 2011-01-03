<?php
class AdminController extends Controller
{
	function hello(){
		$this->render('home',array('title'=>'admin hello'));
	}
}
