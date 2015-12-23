<?php

namespace CpdnAPI\Controllers;

use Phalcon\Http\Response;

class IndexController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
	}
	public function indexAction() {
		$response = new Response ();
		$response->setStatusCode ( 403, "Forbidden" );
		$response->setJsonContent ( array (
				"msg" => "This is a private API which requires an authorized access." 
		) );
		return $response;
	}
}

