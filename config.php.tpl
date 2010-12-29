<?php

// http://it.php.net/manual/en/timezones.php
date_default_timezone_set("Europe/Rome");

/*
 * Enable Debug
 */
define("DEBUG", true);

/**
 * DATABASE
 */

define('DB_NAME',		'christmastweets');
define('DB_USR',		'dbuser');
define('DB_PWD',		'cit3lgr0up');

/*
 * Application Settings
 */
define('START_LAT', 		'41.899279');
define('START_LNG', 		'12.502441');
define('TWITTER_HASH',		'#xmasTweet');
define('TWITTER_POST_FORMAT',_('Ho inviato gli auguri a %s tramite %s : %s... %s'));


/**
 * Twitter Application Keys
 */
define('CONSUMER_KEY', 		'o8HbamMkgk9SfjAHfL3SCA');
define('CONSUMER_SECRET', 	'LdyZ6MLJAjG2JXwXbjoH5i4KaAADk1rISxXiUf1RqU');
define('OAUTH_CALLBACK', 	'http://christmastweet.niko/content/twitterCallback/');

/**
 * Bit.ly
 */
define("BITLY_LOGIN",		"kiuiapps");
define("BITLY_APIKEY",		"R_a5a2f649282501c11e02d89cef344a1a");
define("BITLY_ENDPOINT",	"http://api.bit.ly/v3/");


/**
 * Quova geoIp service
 * config
 */
define('QUOVA_ENDPOINT','http://api.quova.com/v1/ipinfo/');
define('QUOVA_APIKEY','100.rxjm2hnrvu3k6fpc7a3d');
define('QUOVA_SECRET','FphDjrFC');
?>