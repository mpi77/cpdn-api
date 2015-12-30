<?php

namespace CpdnAPI\Controllers;

class IndexController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
		$this->response->setContentType('application/json', 'UTF-8');
	}
	public function indexAction() {
		$this->response->setStatusCode ( 403, "Forbidden" );
		$this->response->setJsonContent ( array (
				"msg" => "This is a private API which requires an authorized access." 
		) );
		return $this->response;
	}
}

