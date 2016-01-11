<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Models\Network\Node;
use CpdnAPI\Models\Network\NodeCalc;
use CpdnAPI\Models\Network\NodeSpec;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\Network\MapPoint;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\CollectionGenerator as CG;
use CpdnAPI\Utils\ItemGenerator as IG;
use CpdnAPI\Utils\MetaGenerator as MG;
use CpdnAPI\Utils\ResponseGenerator as RG;
use CpdnAPI\Utils\Searchable;
use CpdnAPI\Utils\Sortable;
use CpdnAPI\Utils\Expandable;
use CpdnAPI\Utils\Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PQB;

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
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($node) {
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"calc",
							"mapPoint",
							"scheme",
							"spec" 
					) );
					
					$content = array (
							"calc" => "",
							"mapPoint" => "",
							"scheme" => "",
							"spec" => "" 
					);
					
					if (in_array ( "calc", $expandable )) {
						$content ["calc"] = IG::generate ( sprintf ( "/%s/nodes/%s/calc", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id,
								"tsCreate" => $node->calc->tsCreate,
								"tsUpdate" => $node->calc->tsUpdate 
						), NodeCalc::getCalc ( $node->calc ) );
					} else {
						$content ["calc"] = MG::generate ( sprintf ( "/%s/nodes/%s/calc", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id 
						) );
					}
					
					if (in_array ( "mapPoint", $expandable )) {
						$content ["mapPoint"] = IG::generate ( sprintf ( "/%s/mapPoints/%s", Common::API_VERSION_V1, $node->mapPoint->id ), array (
								MG::KEY_ID => $node->mapPoint->id,
								"tsCreate" => $node->mapPoint->tsCreate,
								"tsUpdate" => $node->mapPoint->tsUpdate 
						), MapPoint::getMapPoint ( $node->mapPoint ) );
					} else {
						$content ["mapPoint"] = MG::generate ( sprintf ( "/%s/mapPoints/%s", Common::API_VERSION_V1, $node->mapPoint->id ), array (
								MG::KEY_ID => $node->mapPoint->id 
						) );
					}
					
					if (in_array ( "scheme", $expandable )) {
						$content ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s", Common::API_VERSION_V1, $node->scheme->id ), array (
								MG::KEY_ID => $node->scheme->id,
								"tsCreate" => $node->scheme->tsCreate,
								"tsUpdate" => $node->scheme->tsUpdate 
						), Scheme::getScheme ( $node->scheme ) );
					} else {
						$content ["scheme"] = MG::generate ( sprintf ( "/%s/schemes/%s", Common::API_VERSION_V1, $node->scheme->id ), array (
								MG::KEY_ID => $node->scheme->id 
						) );
					}
					
					if (in_array ( "spec", $expandable )) {
						$content ["spec"] = IG::generate ( sprintf ( "/%s/nodes/%s/spec", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id,
								"tsCreate" => $node->spec->tsCreate,
								"tsUpdate" => $node->spec->tsUpdate 
						), NodeSpec::getSpec ( $node->spec ) );
					} else {
						$content ["spec"] = MG::generate ( sprintf ( "/%s/nodes/%s/spec", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id 
						) );
					}
					
					$r = IG::generate ( $this->request->getURI (), array (
							MG::KEY_ID => $node->id,
							"tsCreate" => $node->tsCreate,
							"tsUpdate" => $node->tsUpdate 
					), $content );
					
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( $r );
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
		return $this->response;
	}
	public function deleteItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readCalcItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($node) {
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( IG::generate ( sprintf ( "/%s/nodes/%s/calc", Common::API_VERSION_V1, $node->id ), array (
							MG::KEY_ID => $node->id,
							"tsCreate" => $node->calc->tsCreate,
							"tsUpdate" => $node->calc->tsUpdate 
					), NodeCalc::getCalc ( $node->calc ) ) );
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
		return $this->response;
	}
	public function updateCalcItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readMapPointItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($node) {
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( IG::generate ( sprintf ( "/%s/mapPoints/%s", Common::API_VERSION_V1, $node->mapPoint->id ), array (
							MG::KEY_ID => $node->mapPoint->id,
							"tsCreate" => $node->mapPoint->tsCreate,
							"tsUpdate" => $node->mapPoint->tsUpdate 
					), MapPoint::getMapPoint ( $node->mapPoint ) ) );
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
		return $this->response;
	}
	public function updateMapPointItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readSchemeItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($node) {
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( IG::generate ( sprintf ( "/%s/schemes/%s", Common::API_VERSION_V1, $node->scheme->id ), array (
							MG::KEY_ID => $node->scheme->id,
							"tsCreate" => $node->scheme->tsCreate,
							"tsUpdate" => $node->scheme->tsUpdate 
					), Scheme::getScheme ( $node->scheme ) ) );
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
		return $this->response;
	}
	public function updateSchemeItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
	public function readSpecItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($node) {
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( IG::generate ( sprintf ( "/%s/nodes/%s/spec", Common::API_VERSION_V1, $node->id ), array (
							MG::KEY_ID => $node->id,
							"tsCreate" => $node->spec->tsCreate,
							"tsUpdate" => $node->spec->tsUpdate 
					), NodeSpec::getSpec ( $node->spec ) ) );
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
		return $this->response;
	}
	public function updateSpecItemAction() {
		$this->response->setStatusCode ( 501, "Not Implemented" );
		return $this->response;
	}
}