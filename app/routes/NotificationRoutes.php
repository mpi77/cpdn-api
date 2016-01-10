<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class NotificationRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Notifications',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/notifications', implode ( "|", Common::getApiVersions () ) ) );
		
		// default CRUD routes
		
		/*
		 * Read notification collection.
		 * Method: GET
		 * URL: /{version}/notifications
		 */
		$this->addGet ( '/:params', array (
				'action' => 'readCollection' 
		) );
		
		/*
		 * Read notification item.
		 * Method: GET
		 * URL: /{version}/notifications/{id}
		 */
		$this->addGet ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readItem' 
		) );
		
		/*
		 * Update notification item.
		 * Method: POST
		 * URL: /{version}/notifications/{id}
		 */
		$this->addPost ( '/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'updateItem'
		) );
	}
}