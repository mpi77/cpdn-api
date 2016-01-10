<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class UserRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Users',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/users', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read user collection.
		 * Method: GET
		 * URL: /{version}/users
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Read user item.
		 * Method: GET
		 * URL: /{version}/users/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
	}
}