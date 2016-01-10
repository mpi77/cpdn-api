<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class MapPointRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'MapPoints',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/mapPoints', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read mapPoint collection.
		 * Method: GET
		 * URL: /{version}/mapPoints
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Create mapPoint item.
		 * Method: POST
		 * URL: /{version}/mapPoints
		 */
		$this->addPost ( '/:params', array (
				'action' => 'createItem'
		) );
		
		/*
		 * Read mapPoint item.
		 * Method: GET
		 * URL: /{version}/mapPoints/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
		
		/*
		 * Update mapPoint item.
		 * Method: POST
		 * URL: /{version}/mapPoints/{id}
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'updateItem'
		) );
		
		/*
		 * Delete mapPoint item.
		 * Method: DELETE
		 * URL: /{version}/mapPoints/{id}
		 */
		$this->addDelete ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'deleteItem'
		) );
	}
}