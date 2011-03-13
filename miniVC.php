<?php
/**
 * 
 * MiniVC is a simple, one-file PHP 5 Framework, it implements a basic [MVC design pattern](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller).
 * 
 * ##the M
 * More specifically it miss the M (Model) allowing everyone to use whatever DB wrapper or ORM one likes, (personally I often use [AdoDB Active record](http://phplens.com/lens/adodb/docs-active-record.htm)).
 * I intentionally missed a personal Model implementation because this is a really basic framework to quick realize small application which focuses on business logic and of small dimension, and cause PHP is full of fantastic DB wrapper and ORM library.
 * 
 * ##The C
 * The controller is a basic Controller as a lot of other Framework, it has some facility method to do GET/POST calls with cURL, it could be  more complex but the idea is to keep things small.   
 * All controller must be named _name_Controller.php.  
 * All the framework expects to have a working mod_rewrite.  
 * The urls are parsed basically with this pattern **""/controller_name/action/param1/param2"** which will instantiate a controller named **controller_name** and fire a method named **action** with **param1** and **param2** as parameters in the call.
 * 
 * 	$contr = new controller_name();  
 * 	$contr->action("parm1","param2");
 * 
 * to override this default behaviour we can setup a static variable in the MiniVC class named $urls that is an assoc array that maps url regex (ex. "^/add_film/([0-9]+/$)") to another url string that represent a valid call as described above.   
 * This is inspired by the way [Django (a Python Framework)](http://www.djangoproject.com/) manages urls and firing actions.
 * 
 * ##The V
 * The managing of view is also simple and basic. From the controller after have done with our business logic we can (or not if we wanto to output json or xml for example) call a method to render a layout (the V) passing it an array of vars that will be available from the layout file.   
 * There's a built in hierarchical layout system, also inspiresd by Django framework. It lets use hereditary in templates and lets include html fragment in other layout.   
 * 
 * ##Filesystem layout
 * The Framework defaults to this filesystem structure and i suggest to use this:
 * 
 * 	rootDirectory/  
 * 		public/   
 * 			.htaccess		//forward to index.php all request write it as you like   
 * 			index.php 		//includes the framework and init it (a sort of bootstrap)   
 * 			css/   
 * 			js/   
 * 			img/   
 * 		classes/   
 * 			yourController.php   
 * 			anotherController.php    
 * 		layout/   
 * 			your_template.php   
 * 			another_template   
 * 
 * Any way all can be set differently
 * 
 * ##Conclusion
 * For a use guide just see the basic project it is very simple and documented.   
 * Every help is a lot appreciated!
 *  
 * @author mogui
 * @version 1.0
 * @copyright mogui, 13 March, 2011
 * @package miniVC
 **/



define('APP_PATH', dirname(__FILE__));


/**
 * Basic MVC handler
 *
 * @package miniVC
 * @author mogui
 **/
class miniVC 
{
	/**
	 * directory from which are taken layout files relatives to APP_PATH
	 * defaults to "layout"
	 * @var string
	 **/
	private static $layout_directory = 'layout';
	
	/**
	 * directory from which are taken controller files relatives to APP_PATH
	 * defaults to "classes"
	 *
	 * @var string
	 **/
	private static $controller_directory = 'classes';
	
	/**
	 * associative array of urls and controller/action callback
	 * simil-django urls, to override default route
	 *
	 * @var array
	 **/
	private static $urls = null;
	
	/**
	 * Array that store values
	 * as a Registry to have global objects
	 * @var unknown_type
	 */
	private static $registry = array();
	
	/**
	 * var that stores the instance of the miniVC
	 * (implement the singleton)
	 * @var miniVC
	 **/
	private static $instance;
	
	/**
	 * debug trigger
	 *
	 * @var string
	 **/
	public static $debug = false;
	
	/**
	 * Private constructor
	 */
	private function __construct(){
		
	}// private function __construct()
	
	/**
	 * Singleton retrieve
	 */
	public static function getInstance() { 
		if(!self::$instance) { 
			self::$instance = new miniVC(); 
	    }
		return self::$instance ;
	} // public static function getInstance()
	
		
	/**
	 * Manage url routing
	 * Takes the uri and first try to match it against self::$urls array (an assoc array 
	 * that maps regex uri => /controller/action ) if it doesn't find anything in that match in the array it treats
	 * the uri as /controller/action
	 * 
	 * 
	 * @param unknown_type $request_uri
	 * @param unknown_type $frontPageCallback
	 */
	public function route($request_uri, $frontPageCallback=null){
		$tmp2 = array_filter(explode("?", $request_uri));
		$clean_uri = $tmp2[0];
		
		// registering autoload
		spl_autoload_register(array($this, 'customLoader'));
		
		if(self::$urls != null){
			// swap the url matching with the call using clean_uri
			foreach(self::$urls as $url => $call){
				$new = preg_replace("|$url|sm",$call, $clean_uri);
				if($new != $clean_uri){
					$clean_uri = $new;
					break;
				}
			}
		}
		
		// we explode teh current uri 
		$tmp = array_filter(explode("/", $clean_uri));
		
		if (count($tmp)==0) {
			// we are in the Front Page
			if($frontPageCallback != null){
				// we MUST have a special call back for the front page
				call_user_func($frontPageCallback);
			}else{
				return false;
			}
		} elseif (count($tmp)<2){
			// Not enough parameters giving 404
			self::manageError("Not enough parameters in URI");
		} else {
			// OK
			$controller = ucfirst(array_shift($tmp)) .'Controller';
			$method = array_shift($tmp);

			// We fire up the methods
			try {
				if (method_exists($controller, $method)){
					$obj = new $controller;
					call_user_func_array(array($obj, $method), $tmp);
				}else{
					self::manageError("Methods or Class doesn't exist");
				}
			} catch (Exception $e){
				self::manageError($e->getMessage());
			}			
		}		
		return true;
	} // public static function route
	
	
	
	/**
	 * Custom dynamic loader
	 *
	 * @return void
	 * @author mogui
	 **/
	private function customLoader($className) {
		$classFile = APP_PATH . '/' .self::$controller_directory. '/'.$className.'.php';

		if(file_exists($classFile)) {
	    	include ( $classFile );
	    } else {
	    	throw new Exception(_("Class $className not exist."));
	    }
	} // END private function customLoader($className) 
	
	
	
	
	/**
	 * gestisce gli errori
	 * 
	 * @param unknown_type $error
	 */
	private function manageError($error) {
		if(self::$debug){
			echo "<h2>Error</h2><p>$error</p>";
			die();
		}else{
			Controller::render404();
		}
	}// public static function manageError
	
	/*
	 * SETTERS AND GETTERS -------------------------------------------------------------------------------------------
	 */
	
	/**
	 * Set layout directory
	 *
	 * @return void
	 * @author mogui
	 **/
	public function setLayoutDirectory($dir){
		self::$layout_directory = $dir;
	}
	
	/**
	 * Get layout directory
	 *
	 * @return void
	 * @author mogui
	 **/
	public function getLayoutDirectory(){
		return self::$layout_directory;
	}
	
	/**
	 * Set controller directory
	 *
	 * @return void
	 * @author mogui
	 **/
	public function setControllerDirectory($dir){
		self::$controller_directory = $dir;
	}
	
	/**
	 * Set urls
	 *
	 * @return void
	 * @author mogui
	 **/
	public function setUrls($urls){
		self::$urls = $urls;
	}
	
	
	/**
	 * Set a value in the registry
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $var
	 */
	public static function registrySet($key,$var){
		self::$registry[$key] = $var;
	}
	
	
	/**
	 * get a value from the registry
	 * 
	 * @param unknown_type $key
	 * @throws Exception
	 */
	public static function registryGet($key){
		if(isset(self::$storage[$key])){
			return self::$storage[$key];
		}else{
			throw new Exception("Value not Found in the registry");
		}
	}
	
} // END class miniVC 






/**
 * Basic Controller
 *
 * All controllers must extend from this class, it has some facility methods to manage request, redirect, and ajax
 *
 * @package miniVC
 * @author mogui
 **/
class Controller 
{
	/**
	 * Do a GET request with cURL
	 * using the class static method
	 * 
	 * @param string $url
	 * @param string $data
	 */
	protected function getRequest($url, $data=null){
		return self::_getRequest($url, $data);
	}// protected function getRequest
	
	
	
	/**
	 * Check if the current request is Ajax
	 */
	protected function isAjax(){
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) return true;
			else return false;
	} // private function isAjax
	
	
	/**
	 * Clean a single string value
	 * 
	 * @param string $value
	 */
	protected function cleanString($value, $escapehtml=false){
		if (get_magic_quotes_gpc()) $value = stripslashes($value);
		if (!is_numeric($value)) $value = mysql_real_escape_string($value);
		if ($escapehtml) $value = strip_tags($value);
		
		return $value;
	}// protected function cleanString
	
	
	/**
	 * Return cleaned value Array
	 * 
	 * @param array $mixed
	 */
	protected function cleanArray(array $array, $escapehtml=false){
		array_walk($array, array($this,'_cleanArray'), $escapehtml);	
		return $array;
	}// protected function clean
	
	
	/**
	 * helper function for cleanArray
	 * 
	 * @param unknown_type $value
	 */
	protected function _cleanArray(&$value, $key, $escapehtml=false){
		$value = $this->cleanString($value, $escapehtml);
	}// protected function clean
	
	
	/**
	 * Renders a layout and output it
	 * 
	 * @param unknown_type $layout
	 * @param unknown_type $contextArray
	 */
	protected function render($layout, $contextArray=array()){
		echo self::_render($layout, $contextArray);
		exit();
	} // protected function render
	
	
	
	
	/**
	 * Renders a layout and returns it as a string
	 * also manages the basic layout system inspired fro django template system
	 * 
	 * @param string $layout
	 * @param array $contextArray
	 */
	public static function _render($layout, $contextArray=array(),$strip= true){
		$filename = APP_PATH .'/'. miniVC::getLayoutDirectory() .'/'. $layout .'.php';
		if(file_exists($filename)){
			ob_start();
			// get the context
			$context = (object) $contextArray;
			
			// get the layout
			include $filename;
			
			// getting the output buffer
			$ret = ob_get_contents();
			ob_end_clean();
			
			/*
			* Manage A layout includes
			*/
			if( preg_match("|/\*\*[\s]+include[\s]+([A-Za-z0-9\/_-]+)[\s]+\*\*/|sm", $ret, $matches) ) {	
				$include_layout = $matches[1];
				$ret = str_replace($matches[0], self::_render($include_layout, $contextArray), $ret);
			}
			
			
			/**
			* Manage the extending of a layout
			*/
			if( preg_match("|/\*\*[\s]+extends[\s]+([A-Za-z0-9_-]+)[\s]+\*\*/|sm", $ret, $matches) ) {
				$father_layout = $matches[1];
				$subs = array();
				
				// Extracting all boxes
				preg_match_all("|/\*\*[\s]+block[\s]+([A-Za-z0-9_-]+)[\s]+\*\*/(.*?)/\*\*[\s]+endblock[\s]+\*\*/|sm", $ret, $blocks);
				
				for ($i=0;$i<count($blocks[0]);$i++) {
					$subs[$blocks[1][$i]] = $blocks[2][$i];
				}
				
				// render the father layout
				$father = self::_render($father_layout,$contextArray,false);				
				preg_match_all("|/\*\*[\s]+block[\s]+([A-Za-z0-9_-]+)[\s]+\*\*/(.*?)/\*\*[\s]+endblock[\s]+\*\*/|sm", $father, $father_blocks );
				
				for ($i=0;$i<count($father_blocks[0]);$i++) {
					$block_name = $father_blocks[1][$i];
					$subs[$block_name] = preg_replace("|/\*\*[\s]+super[\s]+\*\*/|sm", $father_blocks[2][$i], $subs[$block_name]);
					$father = str_replace($father_blocks[0][$i], $subs[$block_name], $father);
				}
				$ret = $father;
				
				// clean unused block tags
				$ret = preg_replace("|/\*\*.*?\*\*/|sm","",$ret);

			}
			
			// clean our silly tags			
			if($strip)
				$ret = preg_replace("|/\*\*.*?\*\*/|sm","",$ret);
			return $ret;
		}else{
			throw new Exception("Missing Layout named: $layout");
		}
	} // public static function _render
	
	
	/**
	 * A GET request with cURL
	 * 
	 * @param string $url
	 * @param mixed $data
	 */
	public static function _getRequest($url, $data=null){
		if($data != null && is_array($data)){
			foreach($data as $key => $val){
				$tmpArr[] = "{$key}={$val}";
			}
			$get_req = implode("&",$tmpArr);
		}
		
		$ch = curl_init($url."?".$get_req);
		curl_setopt($ch, CURLOPT_POSTFIELDS, null);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
		
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
		$data = curl_exec($ch);
		return $data;
	}// protected function _getRequest	
	
	/**
	 * render the 404 layout sending right header response
	 */
	public static function render404(){
		header('HTTP/1.0 404 NOT FOUND');
		if(file_exists(APP_PATH . '/'.miniVC::getLayoutDirectory().'/404.php')){
			echo self::_render('404');
		}else{
			echo '<h1>404 Not Found</h1>';
		}
		exit(); 
	} // public static function render404
	
	
} // END class Controller




?>