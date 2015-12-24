<?php
//define("APP_DIR", "/home/martin/public_html/phalcon/cpdn-api/app");
return new \Phalcon\Config ( array (
		'database' => array (
				"network" => array(
						'adapter' => 'Mysql',
						'host' => 'localhost',
						'username' => 'cpdn',
						'password' => 'cpdn',
						'name' => 'cpdn-network',
						'charset'  => 'utf8'),
				"background" => array(
						'adapter' => 'Mysql',
						'host' => 'localhost',
						'username' => 'cpdn',
						'password' => 'cpdn',
						'name' => 'cpdn-background',
						'charset'  => 'utf8'),
				"idp" => array(
						'adapter' => 'Mysql',
						'host' => 'localhost',
						'username' => 'cpdn',
						'password' => 'cpdn',
						'name' => 'cpdn-idp',
						'charset'  => 'utf8')
		),
		'application' => array (
				'controllersDir' => APP_DIR . '/controllers/',
				'modelsDir' => APP_DIR . '/models/',
				'libraryDir' => APP_DIR . '/library/',
				'pluginsDir' => APP_DIR . '/plugins/',
				'transactionsDir' => APP_DIR . '/transactions/',
				'cacheDir' => APP_DIR . '/cache/',
				'routesDir' => APP_DIR . '/routes/',
				'baseUri' => '/',
				'publicUrl' => 'localhost:488',
				'cryptSalt' => 'IEW+71alkDfR|_&G&f,eEsjd+vU]:k@32:jASJDWE@#a8asd4;pfdskW*JJFeasda7nkAFr!!A&23' 
		),
		'mail' => array(
				'fromName' => 'CPDN API',
				'fromEmail' => 'noreply@cpdn',
				'smtp' => array(
						'server' => 'smtp...',
						'port' => 587,
						'security' => 'tls',
						'username' => 'noreply@...',
						'password' => 'pass...'
				)
		),
		'models' => array (
				'metadata' => array (
						'adapter' => 'Memory' 
				) 
		) 
) );
