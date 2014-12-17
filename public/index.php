<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 * 
 * //check out Tine2 for an excellent example of integrating ZF2, Ciphers, OpenOffice, ExtJs, & more!
 */
 

/**
* Display all errors when APPLICATION_ENV is development.
*/
if ($_SERVER['APPLICATION_ENV'] == 'development') {
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}
//define application root path
defined('ROOT_PATH') or define('ROOT_PATH', __DIR__.'/../');

$time_start = microtime(true);


chdir( dirname(__DIR__) );

// Setup autoloading
include 'init_autoloader.php';

$setloc= setlocale(LC_ALL, 'ja_JP.UTF8', 'ja_JP.eucJP', 'en_US.UTF8'); 
date_default_timezone_set('Asia/Tokyo');

define('REQUEST_MICROTIME', microtime(true));

// Run the application!
Zend\Mvc\Application::init(include 'config/application.config.php')->run();



// log profiling information
$time_end = microtime(true);
$time = $time_end - $time_start;

if(function_exists('memory_get_peak_usage')) {
    $memory = memory_get_peak_usage(true);
} else {
    $memory = memory_get_usage(true);
}

if(function_exists('realpath_cache_size')) {
    $realPathCacheSize = realpath_cache_size();
} else {
    $realPathCacheSize = 'unknown';
}


//TODO: employ logging function here