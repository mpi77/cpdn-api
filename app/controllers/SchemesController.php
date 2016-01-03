<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\CollectionGenerator as CG;
use CpdnAPI\Utils\Searchable;
use CpdnAPI\Utils\Sortable;
use Phalcon\Mvc\Model\Criteria;

class SchemesController extends ControllerBase {
	private $validFields = array (
			"id" => "/^[1-9][0-9]{0,9}$/",
			"tsCreate" => Common::PATTERN_ISO_8601,
			"tsUpdate" => Common::PATTERN_ISO_8601,
			"description" => "/^[a-zA-Z0-9_\/\.\-]{0,45}$/",
			"lock" => Common::PATTERN_BOOLEAN,
			"name" => "/^[a-zA-Z0-9_\/\.\-]{1,45}$/",
			"version" => "/^[1-9][0-9]{0,9}$/" 
	);
	public function initialize() {
		$this->view->disable ();
		$this->response->setContentType ( 'application/json', 'UTF-8' );
		$this->response->setStatusCode ( 404, "Not Found" );
	}
	public function readCollectionAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$this->response->setStatusCode ( 200, "OK" );
				
				$query = Criteria::fromInput ( $this->di, 'CpdnAPI\Models\Network\Scheme', Searchable::buildCriteriaFromInputParams($this->request->get ( "q" ),$this->validFields) );
				
				if ($s = Sortable::buildCriteriaOrderByParams($this->request->get ( "s" ),$this->validFields)) {
					$query->orderBy ( $s );
				}
				
				$schemes = Scheme::find ( $query->getParams () );
				$r = array ();
				
				foreach ( $schemes as $s ) {
					$r [] = array (
							$s->id,
							$s->name 
					);
				}
				$this->response->setJsonContent ( CG::generate($r) );
				
				return $this->response;
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