<?php
class HelloController extends Controller
{
	function world(){
		$this->render('hello_world',array('title'=>'Hello World!'));
	}
	
	function extends_world($a_param){
		// tremendous operation to get $foo
		$foo = $a_param;
		
		$this->render('extends_world',array(
			'title'=>'Extends Hello World!',
			'foo' => $foo
		));
	}
}
