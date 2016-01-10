<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;

class ObjectsController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
		$this->response->setContentType ( 'application/json', 'UTF-8' );
		$this->response->setStatusCode ( 404, "Not Found" );
	}
	public function readCollectionAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function createItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function updateItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function deleteItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readNodesCollectionAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function createNodeItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function deleteNodeItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
}