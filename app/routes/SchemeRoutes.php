<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class SchemeRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Schemes',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/schemes', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read scheme collection.
		 * Method: GET
		 * URL: /{version}/schemes
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Create scheme item.
		 * Method: POST
		 * URL: /{version}/schemes
		 */
		$this->addPost ( '/:params', array (
				'action' => 'createItem'
		) );
		
		/*
		 * Read scheme item.
		 * Method: GET
		 * URL: /{version}/schemes/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
		
		/*
		 * Update scheme item.
		 * Method: POST
		 * URL: /{version}/schemes/{id}
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'updateItem'
		) );
		
		/*
		 * Delete scheme item.
		 * Method: POST
		 * URL: /{version}/schemes/{id}
		 */
		$this->addDelete ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'deleteItem'
		) );
		
		// aditional routes
		
		/*
		 * Read scheme/nodes collection.
		 * Method: GET
		 * URL: /{version}/schemes/{id}/nodes
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/nodes/:params', array (
				'action' => 'readNodesCollection'
		) );
		
		/*
		 * Read scheme/sections collection.
		 * Method: GET
		 * URL: /{version}/schemes/{id}/sections
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/sections/:params', array (
				'action' => 'readSectionsCollection'
		) );
		
		/*
		 * Read scheme/mapPoints collection.
		 * Method: GET
		 * URL: /{version}/schemes/{id}/mapPoints
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/mapPoints/:params', array (
				'action' => 'readMapPointsCollection'
		) );
		
		/*
		 * Read scheme/objects collection.
		 * Method: GET
		 * URL: /{version}/schemes/{id}/objects
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/objects/:params', array (
				'action' => 'readObjectsCollection'
		) );
		
		/*
		 * Read scheme/permissions collection.
		 * Method: GET
		 * URL: /{version}/schemes/{id}/permissions
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/permissions/:params', array (
				'action' => 'readPermissionsCollection'
		) );
		
		/*
		 * Read scheme/tasks collection.
		 * Method: GET
		 * URL: /{version}/schemes/{id}/tasks
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/tasks/:params', array (
				'action' => 'readTasksCollection'
		) );
	}
}