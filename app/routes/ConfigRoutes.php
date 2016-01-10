<?php

namespace CpdnAPI\Routes;

use CpdnAPI\Utils\Common;
use Phalcon\Mvc\Router\Group as RouterGroup;

class ConfigRoutes extends RouterGroup {
	public function initialize() {
		$this->setPaths ( array (
				'controller' => 'Configs',
				'namespace' => 'CpdnAPI\Controllers' 
		) );
		
		$this->setPrefix ( sprintf ( '/{version:(%s)}/configs', implode ( "|", Common::getApiVersions () ) ) );
		
		// aditional routes
		
		/*
		 * Read app configs collection.
		 * Method: GET
		 * URL: /{version}/configs/{app}
		 */
		$this->addGet ( '/{app:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readAppCollection' 
		) );
		
		/*
		 * Read app config item.
		 * Method: GET
		 * URL: /{version}/configs/{app}/{id}
		 */
		$this->addGet ( '/{app:([a-zA-Z0-9]+)}/{id:([a-zA-Z0-9]+)}/:params', array (
				'action' => 'readAppItem' 
		) );
	}
}