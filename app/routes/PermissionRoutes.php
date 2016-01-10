<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class PermissionRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Permissions',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/permissions', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read permission collection.
		 * Method: GET
		 * URL: /{version}/permissions
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Create permission item.
		 * Method: POST
		 * URL: /{version}/permissions
		 */
		$this->addPost ( '/:params', array (
				'action' => 'createItem'
		) );
		
		/*
		 * Read permission item.
		 * Method: GET
		 * URL: /{version}/permissions/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
		
		/*
		 * Update permission item.
		 * Method: POST
		 * URL: /{version}/permissions/{id}
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'updateItem'
		) );
		
		/*
		 * Delete permission item.
		 * Method: DELETE
		 * URL: /{version}/permissions/{id}
		 */
		$this->addDelete ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'deleteItem'
		) );
	}
}