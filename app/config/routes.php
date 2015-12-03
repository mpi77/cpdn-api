<?php
$router = new Phalcon\Mvc\Router ();
$router->setDefaultController ( 'index' );
$router->setDefaultAction ( 'index' );

/* public area */
$router->add ( '/welcome', array (
		'controller' => 'index',
		'action' => 'index' 
) );

/* private area */

return $router;
