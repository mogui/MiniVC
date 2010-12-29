<?php
class miniMVC 
{
	/**
	 * directory from which are taken layout files relatives to APP_PATH
	 * defaults to "layout"
	 * @var string
	 **/
	protected static $layout_directory = 'layout';
	
	
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
	
	
	/* STATIC METHODS ---------------------------------------------------------------------------- */
	
	
	
	/**
	 * Renderizza un layout e lo ritorna
	 * 
	 * @param unknown_type $layout
	 * @param unknown_type $contextArray
	 */
	public static function _render($layout, $contextArray=array()){
		$filename = APP_PATH .'/'. self::$layout_directory .'/'. $layout .'.php';
		if(file_exists($filename)){
			ob_start();
			$context = (object) $contextArray;
			include $filename;
			$ret = ob_get_contents();
			ob_end_clean();
			return $ret;
		}else{
			throw new Exception(_("Missing Layout named: ").$layout);
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
		if(file_exists(APP_PATH . '/layout/404.php')){
			echo self::_render('404');
		}
		exit(); 
	} // public static function render404
	
	
	
	/**
	 * gestisce gli errori
	 * 
	 * @param unknown_type $error
	 */
	public static function manageError($error) {
		if(DEBUG){
			echo "<h2>Error</h2><p>$error</p>";
			die();
		}else{
			self::render404();
		}
	}// public static function manageError
	
	
	
	/**
	 * Gestisce il routing delle url
	 * in maniera molto semplice cerca di far partire classe e methodo altrimenti da 404
	 * se invece � la front page oh usa una callback o torna falso
	 * 
	 * @param unknown_type $request_uri
	 * @param unknown_type $frontPageCallback
	 */
	public static function route($request_uri, $frontPageCallback=null){
		$tmp2 = array_filter(explode("?", $request_uri));
		$tmp = array_filter(explode("/", $tmp2[0]));
		
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
				self::manageError($e->getMessage());
			}			
		}		
		return true;
	} // public static function route
	
	
	
	/*
	 * SETTERS
	 */
	
	/**
	 * Set layout directory
	 *
	 * @return void
	 * @author mogui
	 **/
	public static function setLayoutDirectory($dir){
		self::$layout_directory = $dir;
	}
	
}





/**
 * Registry
 * 
 * Semplice registro statico!
 * @author niko
 *
 */ 
class Registry
{
	/**
	 * Array that store values 
	 * @var unknown_type
	 */
	private static $storage=array();
	
	
	/**
	 * Set a value in the registry
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $var
	 */
	public static function set($key,$var){
		self::$storage[$key] = $var;
	}
	
	
	/**
	 * get a value from the registry
	 * 
	 * @param unknown_type $key
	 * @throws Exception
	 */
	public static function get($key){
		if(isset(self::$storage[$key])){
			return self::$storage[$key];
		}else{
			throw new Exception("Value not Found in the registry");
		}
	}
}





?>