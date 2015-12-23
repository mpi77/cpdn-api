<?php
$router = new Phalcon\Mvc\Router ( false );
$router->setDefaultController ( 'index' );
$router->setDefaultAction ( 'index' );

/* public area */
$router->add ( '/', array (
		'controller' => 'index',
		'action' => 'index' 
) );

/* private area */
$router->add ( '/{version:(v1)}/:controller/:action/:params', array (
		"controller" => 2,
		"action" => 3,
		"params" => 4 
) );

return $router;
