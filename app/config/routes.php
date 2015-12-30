<?php
use Phalcon\Mvc\Router;
use CpdnAPI\Routes\SchemeRoutes;

$router = new Router ();
// $router->removeExtraSlashes(true);
$router->setDefaultController ( 'index' );
$router->setDefaultAction ( 'index' );

/* public area */
$router->add ( '/', array (
		'controller' => 'index',
		'action' => 'index' 
) );

/* private area */
$router->mount ( new SchemeRoutes () );

return $router;