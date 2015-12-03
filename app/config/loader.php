<?php
$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    'CpdnAPI\Models' => $config->application->modelsDir,
    'CpdnAPI\Controllers' => $config->application->controllersDir,
	'CpdnAPI\Plugins' => $config->application->pluginsDir,
	'CpdnAPI\Transactions' => $config->application->transactionsDir,
    'CpdnAPI' => $config->application->libraryDir
));

$loader->register();

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../vendor/autoload.php';
