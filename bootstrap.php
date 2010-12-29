<?php
$app_path = dirname(__FILE__);
define('APP_PATH', $app_path);

include_once(APP_PATH .'/config.php');

include_once(APP_PATH . '/lib/adodb/adodb.inc.php');
include_once(APP_PATH . '/lib/adodb/adodb-active-record.inc.php');
require_once(APP_PATH . '/lib/twitteroauth/twitteroauth.php');
include_once (APP_PATH . '/classes/models.php');

function __autoload($Class) {
	if(file_exists(APP_PATH . '/classes/'.$Class.'.php')) {
    	include APP_PATH . '/classes/'.$Class.'.php';
    } else {
    	throw new Exception(_('Class not exist.'));
    }
}

function __($str){
	echo _($str); 
}

$db = NewADOConnection("mysql://".DB_USR.":".DB_PWD."@localhost/".DB_NAME);
ADOdb_Active_Record::SetDatabaseAdapter($db);
Registry::set("DB", $db);

/*
MANAGING LOCALES
*/
$locale = "en_US";

if (isset($_SESSION['current_locale'])){
	$locale = $_SESSION['current_locale'];
}else{
	$sig = md5(QUOVA_APIKEY . QUOVA_SECRET .  gmdate('U'));
	$tmp =  Controller::_getRequest(QUOVA_ENDPOINT.$_SERVER['REMOTE_ADDR'],array(
		'apikey' => QUOVA_APIKEY,
		'sig' => $sig
	)); 
	$ret = simplexml_load_string($tmp);
	if ($ret->Location->CountryData->country_code == 'it' ){
		$_SESSION['current_locale'] = 'it_IT';
		$locale = "it_IT";
	}
}


putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("default", APP_PATH ."/locale");
textdomain("default");

?>