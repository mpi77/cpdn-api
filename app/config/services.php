<?php
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Crypt;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Events\Manager as EventsManager;
use CpdnAPI\Plugins\NotFoundPlugin;
use CpdnAPI\Mail\Mail;
use CpdnAPI\Auth\Auth;

$di = new FactoryDefault ();

$di->set ( 'config', $config );

$di->set ( 'dispatcher', function () use($di) {
	$eventsManager = new EventsManager ();
	$eventsManager->attach ( 'dispatch:beforeException', new NotFoundPlugin () );
	$dispatcher = new Dispatcher ();
	$dispatcher->setDefaultNamespace ( 'CpdnAPI\Controllers' );
	$dispatcher->setEventsManager ( $eventsManager );
	return $dispatcher;
} );

$di->set ( 'url', function () use($config) {
	$url = new UrlResolver ();
	$url->setBaseUri ( $config->application->baseUri );
	return $url;
}, true );

$di->set ( 'view', function () {
	$view = new View ();
	return $view;
}, true );

$di->set ( 'networkDb', function () use($config) {
	return new DbAdapter ( array (
			'host' => $config->database->network->host,
			'username' => $config->database->network->username,
			'password' => $config->database->network->password,
			'dbname' => $config->database->network->name,
			'charset' => $config->database->network->charset 
	) );
} );

$di->set ( 'backgroundDb', function () use($config) {
	return new DbAdapter ( array (
			'host' => $config->database->background->host,
			'username' => $config->database->background->username,
			'password' => $config->database->background->password,
			'dbname' => $config->database->background->name,
			'charset' => $config->database->background->charset 
	) );
} );

$di->set ( 'idpDp', function () use($config) {
	return new DbAdapter ( array (
			'host' => $config->database->idp->host,
			'username' => $config->database->idp->username,
			'password' => $config->database->idp->password,
			'dbname' => $config->database->idp->name,
			'charset' => $config->database->idp->charset 
	) );
} );

$di->set ( 'modelsMetadata', function () use($config) {
	return new MetaDataAdapter ( array (
			'metaDataDir' => $config->application->cacheDir . 'metaData/' 
	) );
} );

$di->set ( 'session', function () {
	$session = new SessionAdapter ( array (
			'uniqueId' => 'cpdnapi' 
	) );
	$session->start ();
	return $session;
} );

$di->set ( 'crypt', function () use($config) {
	$crypt = new Crypt ();
	$crypt->setKey ( $config->application->cryptSalt );
	return $crypt;
} );

$di->set ( 'router', function () {
	return require __DIR__ . '/routes.php';
} );

$di->set ( 'mail', function () {
	return new Mail ();
} );

$di->set ( 'auth', function () {
	return new Auth ();
} );
	