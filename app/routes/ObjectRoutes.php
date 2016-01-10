<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class ObjectRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Objects',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/objects', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read object collection.
		 * Method: GET
		 * URL: /{version}/objects
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Create object item.
		 * Method: POST
		 * URL: /{version}/objects
		 */
		$this->addPost ( '/:params', array (
				'action' => 'createItem'
		) );
		
		/*
		 * Read object item.
		 * Method: GET
		 * URL: /{version}/objects/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
		
		/*
		 * Update object item.
		 * Method: POST
		 * URL: /{version}/objects/{id}
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'updateItem'
		) );
		
		/*
		 * Delete object item.
		 * Method: DELETE
		 * URL: /{version}/objects/{id}
		 */
		$this->addDelete ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'deleteItem'
		) );
		
		// aditional routes
		
		/*
		 * Read object/nodes collection.
		 * Method: GET
		 * URL: /{version}/objects/{id}/nodes
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/nodes/:params', array (
				'action' => 'readNodesCollection'
		) );
		
		/*
		 * Create object/node item.
		 * Method: POST
		 * URL: /{version}/objects/{objectId}/node/{nodeId}
		 */
		$this->addPost ( '/{objectId:([a-zA-Z0-9]+)}/node/{nodeId:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'createNodeItem'
		) );
		
		/*
		 * Delete object/node item.
		 * Method: DELETE
		 * URL: /{version}/objects/{objectId}/node/{nodeId}
		 */
		$this->addDelete ( '/{objectId:([a-zA-Z0-9]+)}/node/{nodeId:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'deleteNodeItem'
		) );
	}
}