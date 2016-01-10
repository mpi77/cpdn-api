<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class NodeRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Nodes',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/nodes', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read node collection.
		 * Method: GET
		 * URL: /{version}/nodes
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Create node item.
		 * Method: POST
		 * URL: /{version}/nodes
		 */
		$this->addPost ( '/:params', array (
				'action' => 'createItem'
		) );
		
		/*
		 * Read node item.
		 * Method: GET
		 * URL: /{version}/nodes/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
		
		/*
		 * Delete node item.
		 * Method: DELETE
		 * URL: /{version}/nodes/{id}
		 */
		$this->addDelete ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'deleteItem'
		) );
		
		// aditional routes
		
		/*
		 * Read node/calc item.
		 * Method: GET
		 * URL: /{version}/nodes/{id}/calc
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/calc/:params', array (
				'action' => 'readCalcItem'
		) );
		
		/*
		 * Update node/calc item.
		 * Method: POST
		 * URL: /{version}/nodes/{id}/calc
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/calc/:params', array (
				'action' => 'updateCalcItem'
		) );
		
		/*
		 * Read node/mapPoint item.
		 * Method: GET
		 * URL: /{version}/nodes/{id}/mapPoint
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/mapPoint/:params', array (
				'action' => 'readMapPointItem'
		) );
		
		/*
		 * Update node/mapPoint item.
		 * Method: POST
		 * URL: /{version}/nodes/{id}/mapPoint
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/mapPoint/:params', array (
				'action' => 'updateMapPointItem'
		) );
		
		/*
		 * Read node/scheme item.
		 * Method: GET
		 * URL: /{version}/nodes/{id}/scheme
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/scheme/:params', array (
				'action' => 'readSchemeItem'
		) );
		
		/*
		 * Update node/scheme item.
		 * Method: POST
		 * URL: /{version}/nodes/{id}/scheme
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/scheme/:params', array (
				'action' => 'updateSchemeItem'
		) );
		
		/*
		 * Read node/spec item.
		 * Method: GET
		 * URL: /{version}/nodes/{id}/spec
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/spec/:params', array (
				'action' => 'readSpecItem'
		) );
		
		/*
		 * Update node/spec item.
		 * Method: POST
		 * URL: /{version}/nodes/{id}/spec
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/spec/:params', array (
				'action' => 'updateSpecItem'
		) );
	}
}