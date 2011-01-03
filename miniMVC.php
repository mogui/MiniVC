<?php
/**
 * 
 * the default / preferred directory structure is as follow:
 * this file has to be incuded by the index of the site
 * the index is the pnly access point of the app so it's better if all trhe rest is outside web access
 * as this structure suggest
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



define('APP_PATH', dirname(__FILE__));


/**
 * Basic MVC handler
 *
 * @package miniMVC
 * @author mogui
 **/
class miniMVC 
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
	 * as a Registry 
	 * @var unknown_type
	 */
	private static $registry = array();
	
	/**
	 * var that stores the instance of the miniMVC
	 *
	 * @var miniMVC
	 **/
	private static $instance;
	
	/**
	 * debug trigger
	 *
	 * @var string
	 **/
	public static $debug = false;
	
	/**
	 * Privaet constructor
	 */
	private function __construct(){
		
	}// private function __construct()
	
	/**
	 * Singleton retrieve
	 */
	public static function getInstance() { 
		if(!self::$instance) { 
			self::$instance = new miniMVC(); 
	    }
		return self::$instance ;
	} // public static function getInstance()
	
		
	/**
	 * Manage url routing
	 * firstly search 
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
			foreach(self::$urls as $url => $call){
				$new = preg_replace("|$url|sm",$call, $clean_uri);
				if($new != $clean_uri){
					$clean_uri = $new;
					break;
				}
			}
		}
		
		$tmp = array_filter(explode("/", $clean_uri));
		
		if (count($tmp)==0) {
			// Front Page
			if($frontPageCallback != null){
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

			try {
				if (method_exists($controller, $method)){
					$obj = new $controller;
					call_user_func_array(array($obj, $method), $tmp);
				}else{
					self::manageError("Methods or Class doesn't exist");
				}
			} catch (Exception $e){
				$this->manageError($e->getMessage());
			}			
		}		
		return true;
	} // public static function route
	
	
	
	/**
	 * undocumented function
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
	
} // END class miniMVC 






/**
 * Basic Controller
 *
 * @package miniMVC
 * @author mogui
 **/
class Controller 
{
	/**
	 * Fa una richiesta in get
	 * 
	 * @param unknown_type $url
	 * @param unknown_type $data
	 */
	protected function getRequest($url, $data=null){
		
		return self::_getRequest($url, $data);
	}// protected function getRequest
	
	
	
	/**
	 * Controlla se è una richiesta ajax
	 */
	protected function isAjax(){
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) return true;
			else return false;
	} // private function isAjax
	
	
	/**
	 * Clean a value
	 * 
	 * @param unknown_type $value
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
	 * @param unknown_type $mixed
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
	 * Renderizza il layout e lo stampa
	 * 
	 * @param unknown_type $layout
	 * @param unknown_type $contextArray
	 */
	protected function render($layout, $contextArray=array()){
		echo self::_render($layout, $contextArray);
		exit();
	} // private function render
	
	
	
	
	/**
	 * Renders a layout and returns it as a string
	 * 
	 * @param string $layout
	 * @param array $contextArray
	 */
	public static function _render($layout, $contextArray=array(),$strip= true){
		$filename = APP_PATH .'/'. miniMVC::getLayoutDirectory() .'/'. $layout .'.php';
		if(file_exists($filename)){
			ob_start();
			$context = (object) $contextArray;
			include $filename;
			$ret = ob_get_contents();
			ob_end_clean();
			
			
			if( preg_match("|/\*\*[\s]+include[\s]+([A-Za-z0-9\/_-]+)[\s]+\*\*/|sm", $ret, $matches) ) {
				
				$include_layout = $matches[1];
				$ret = str_replace($matches[0], self::_render($include_layout, $contextArray), $ret);
			}
			
			if( preg_match("|/\*\*[\s]+extends[\s]+([A-Za-z0-9_-]+)[\s]+\*\*/|sm", $ret, $matches) ) {
				$father_layout = $matches[1];
				$subs = array();
				preg_match_all("|/\*\*[\s]+block[\s]+([A-Za-z0-9_-]+)[\s]+\*\*/(.*)?/\*\*[\s]+endblock[\s]+\*\*/|sm", $ret, $blocks);
				for ($i=0;$i<count($blocks[0]);$i++) {
					$subs[$blocks[1][$i]] = $blocks[2][$i];
				}
				
				$father = self::_render($father_layout,$contextArray,false);

				preg_match_all("|/\*\*[\s]+block[\s]+([A-Za-z0-9_-]+)[\s]+\*\*/(.*)?/\*\*[\s]+endblock[\s]+\*\*/|sm", $father, $father_blocks );

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
	 * Fa una richiesta in get
	 * 
	 * @param unknown_type $url
	 * @param unknown_type $data
	 */
	public static function _getRequest($url, $data=null){
		if($data != null){
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
	 * Enter description here ...
	 */
	public static function render404(){
		header('HTTP/1.0 404 NOT FOUND');
		if(file_exists(APP_PATH . '/'.miniMVC::getLayoutDirectory().'/404.php')){
			echo self::_render('404');
		}else{
			echo '<h1>404 Not Found</h1>';
		}
		exit(); 
	} // public static function render404
	
	
} // END class Controller




?>