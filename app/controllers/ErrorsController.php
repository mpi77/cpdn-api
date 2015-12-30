<?php

namespace CpdnAPI\Controllers;

class ErrorsController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
		$this->response->setContentType('application/json', 'UTF-8');
	}
	public function e404Action() {
		$this->response->setStatusCode(404, "Not Found");
		return $this->response;
	}
	public function e500Action() {
		$this->response->setStatusCode(500, "Internal Server Error");
		return $this->response;
	}
}
