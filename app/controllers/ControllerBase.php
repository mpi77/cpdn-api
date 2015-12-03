<?php

namespace CpdnAPI\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller {
	public function beforeExecuteRoute(Dispatcher $dispatcher) {
		// check OAuth and ACL
	}
}