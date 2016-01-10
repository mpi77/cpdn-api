<?php
use Phalcon\Mvc\Router;
use CpdnAPI\Routes\ConfigRoutes;
use CpdnAPI\Routes\ExecutorRoutes;
use CpdnAPI\Routes\MappointRoutes;
use CpdnAPI\Routes\NodeRoutes;
use CpdnAPI\Routes\NotificationRoutes;
use CpdnAPI\Routes\ObjectRoutes;
use CpdnAPI\Routes\PathRoutes;
use CpdnAPI\Routes\PermissionRoutes;
use CpdnAPI\Routes\SchemeRoutes;
use CpdnAPI\Routes\SectionRoutes;
use CpdnAPI\Routes\TaskRoutes;
use CpdnAPI\Routes\UserRoutes;

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
$router->mount ( new ConfigRoutes () );
$router->mount ( new ExecutorRoutes () );
$router->mount ( new MappointRoutes () );
$router->mount ( new NodeRoutes () );
$router->mount ( new NotificationRoutes () );
$router->mount ( new ObjectRoutes () );
$router->mount ( new PathRoutes () );
$router->mount ( new PermissionRoutes () );
$router->mount ( new SchemeRoutes () );
$router->mount ( new SectionRoutes () );
$router->mount ( new TaskRoutes () );
$router->mount ( new UserRoutes () );

return $router;