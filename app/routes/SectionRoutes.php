<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class SectionRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Sections',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/sections', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read section collection.
		 * Method: GET
		 * URL: /{version}/sections
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Create section item.
		 * Method: POST
		 * URL: /{version}/sections
		 */
		$this->addPost ( '/:params', array (
				'action' => 'createItem'
		) );
		
		/*
		 * Read section item.
		 * Method: GET
		 * URL: /{version}/sections/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
		
		/*
		 * Delete section item.
		 * Method: DELETE
		 * URL: /{version}/sections/{id}
		 */
		$this->addDelete ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'deleteItem'
		) );
		
		// aditional routes
		
		/*
		 * Read section/calc item.
		 * Method: GET
		 * URL: /{version}/sections/{id}/calc
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/calc/:params', array (
				'action' => 'readCalcItem'
		) );
		
		/*
		 * Update section/calc item.
		 * Method: POST
		 * URL: /{version}/sections/{id}/calc
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/calc/:params', array (
				'action' => 'updateCalcItem'
		) );
		
		/*
		 * Read section/nodes item.
		 * Method: GET
		 * URL: /{version}/sections/{id}/nodes
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/nodes/:params', array (
				'action' => 'readNodesItem'
		) );
		
		/*
		 * Update section/nodes item.
		 * Method: POST
		 * URL: /{version}/sections/{id}/nodes
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/nodes/:params', array (
				'action' => 'updateNodesItem'
		) );
		
		/*
		 * Read section/scheme item.
		 * Method: GET
		 * URL: /{version}/sections/{id}/scheme
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/scheme/:params', array (
				'action' => 'readSchemeItem'
		) );
		
		/*
		 * Update section/scheme item.
		 * Method: POST
		 * URL: /{version}/sections/{id}/scheme
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/scheme/:params', array (
				'action' => 'updateSchemeItem'
		) );
		
		/*
		 * Read section/spec item.
		 * Method: GET
		 * URL: /{version}/sections/{id}/spec
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/spec/:params', array (
				'action' => 'readSpecItem'
		) );
		
		/*
		 * Update section/spec item.
		 * Method: POST
		 * URL: /{version}/sections/{id}/spec
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/spec/:params', array (
				'action' => 'updateSpecItem'
		) );
		
		/*
		 * Read section/paths collection.
		 * Method: GET
		 * URL: /{version}/section/{id}/paths
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/paths/:params', array (
				'action' => 'readPathsCollection'
		) );
	}
}