<?php

namespace CpdnAPI\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller {
	public function beforeExecuteRoute(Dispatcher $dispatcher) {
		
		$r = $this->auth->isAuthorized($this->request);
		$controller = $dispatcher->getControllerName ();
		
		if ($r !== true && $controller != "errors") {
			$dispatcher->forward ( array (
					'controller' => 'errors',
					'action' => 'e403' 
			) );
			return false;
		}
	}
}