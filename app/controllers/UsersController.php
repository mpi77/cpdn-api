<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;

class UsersController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
		$this->response->setContentType ( 'application/json', 'UTF-8' );
		$this->response->setStatusCode ( 404, "Not Found" );
	}
	public function readCollectionAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
}