<?php

namespace CpdnAPI\Controllers;

use Phalcon\Http\Response;

class ErrorsController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
	}
	public function e404Action() {
		$response = new Response();
		$response->setStatusCode(404, "Not Found");
		return $response;
	}
	public function e500Action() {
		$response = new Response();
		$response->setStatusCode(500, "Internal Server Error");
		return $response;
	}
}
