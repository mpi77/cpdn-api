<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;

class NodesController extends ControllerBase {
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
	public function deleteItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readCalcItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function updateCalcItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readMapPointItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function updateMapPointItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readSchemeItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function updateSchemeItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readSpecItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function updateSpecItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
}