<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class TaskRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Tasks',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/tasks', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read task collection.
		 * Method: GET
		 * URL: /{version}/tasks
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Create task item.
		 * Method: POST
		 * URL: /{version}/tasks
		 */
		$this->addPost ( '/:params', array (
				'action' => 'createItem'
		) );
		
		/*
		 * Read task item.
		 * Method: GET
		 * URL: /{version}/tasks/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
	}
}