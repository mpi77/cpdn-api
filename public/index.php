<?php
error_reporting ( E_ALL );
date_default_timezone_set('Europe/Prague');

try {
	
	define ( 'BASE_DIR', dirname ( __DIR__ ) );
	define ( 'APP_DIR', BASE_DIR . '/app' );
	
	$config = include APP_DIR . '/config/config.php';
	
	include APP_DIR . '/config/loader.php';
	include APP_DIR . '/config/services.php';
	
	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application ( $di );
	
	echo $application->handle ()->getContent ();
} catch ( Exception $e ) {
	echo $e->getMessage (), '<br>';
	echo nl2br ( htmlentities ( $e->getTraceAsString () ) );
}