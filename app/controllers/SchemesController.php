<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\CollectionGenerator as CG;
use Phalcon\Mvc\Model\Criteria;

class SchemesController extends ControllerBase {
	private $validFields = array (
			"id" => "",
			"tsCreate" => "",
			"tsUpdate" => "",
			"description" => "",
			"lock" => "",
			"name" => "",
			"version" => "" 
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
				
				// searchable feature
				$q = $this->request->get ( "q" );
				if (is_string ( $q ) && mb_strlen ( $q ) > 0) {
					
					$q = mb_strcut ( $q, 1, mb_strlen ( $q ) - 2 );
					$q_fields = explode ( ";", $q );
					
					$filter = array ();
					foreach ( $q_fields as $q_field ) {
						$qq = explode ( "=", $q_field );
						$key = $qq [0];
						$value = $qq [1];
						
						if (array_key_exists ( $key, $this->validFields ) && is_string ( $value ) && strlen ( $value ) > 0) {
							$filter [$key] = $value;
						}
					}
				}
				
				$query = Criteria::fromInput ( $this->di, 'CpdnAPI\Models\Network\Scheme', $filter );
				
				// sortable feature
				$s = $this->request->get ( "s" );
				if (is_string ( $s ) && mb_strlen ( $s ) > 0) {
					$s = mb_strcut ( $s, 1, mb_strlen ( $s ) - 2 );
					if (array_key_exists ( $s, $this->validFields )) {
						$query->orderBy ( $s );
					}
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