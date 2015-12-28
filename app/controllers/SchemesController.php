<?php

namespace CpdnAPI\Controllers;

use Phalcon\Http\Response;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Utils\Common;

class SchemesController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
	}
	public function readCollectionAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$response = new Response ();
				$response->setStatusCode ( 200, "OK" );
				$schemes = Scheme::find ();
				$r = array ();
				
				foreach ( $schemes as $s ) {
					$r [] = array (
							$s->id,
							$s->name 
					);
				}
				$response->setJsonContent ( $r );
				
				return $response;
				break;
		}
	}
	public function createItemAction() {
	}
	public function readItemAction() {
	}
	public function updateItemAction() {
	}
	public function deleteItemAction() {
	}
	public function readNodesCollectionAction() {
	}
	public function readSectionsCollectionAction() {
	}
	public function readMapPointsCollectionAction() {
	}
	public function readObjectsCollectionAction() {
	}
	public function readPermissionsCollectionAction() {
	}
	public function readTasksCollectionAction() {
	}
}