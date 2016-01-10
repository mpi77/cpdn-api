<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class ExecutorRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Executors',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/executors', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read executor collection.
		 * Method: GET
		 * URL: /{version}/executors
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Read executor item.
		 * Method: GET
		 * URL: /{version}/executors/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
	}
}