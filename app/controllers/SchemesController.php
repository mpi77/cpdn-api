<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\CollectionGenerator as CG;
use CpdnAPI\Utils\ItemGenerator as IG;
use CpdnAPI\Utils\MetaGenerator as MG;
use CpdnAPI\Utils\Searchable;
use CpdnAPI\Utils\Sortable;
use CpdnAPI\Utils\Expandable;
use CpdnAPI\Utils\Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PQB;

class SchemesController extends ControllerBase {
	private $validFields = array (
			"id" => "/^[1-9][0-9]{0,9}$/",
			"tsCreate" => Common::PATTERN_ISO_8601,
			"tsUpdate" => Common::PATTERN_ISO_8601,
			"description" => "/^[a-zA-Z0-9_\/\.\-]{0,45}$/",
			"lock" => "/^(0|1)$/",
			"name" => "/^[a-zA-Z0-9_\/\.\-]{1,45}$/",
			"version" => "/^[1-9][0-9]{0,9}$/" 
	);
	public function initialize() {
		$this->view->disable ();
		$this->response->setContentType ( 'application/json', 'UTF-8' );
		$this->response->setStatusCode ( 404, "Not Found" );
	}
	public function readCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				// create builder
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Network\Scheme' );
				
				// append WHERE conditions
				$where = Searchable::buildQueryBuilderWhereParams ( $this->request->get ( Searchable::URL_QUERY_PARAM ), $this->validFields );
				if (is_array ( $where ) && ! empty ( $where ) && $where !== false && $where !== null) {
					$builder->where ( $where ["conditions"], $where ["bindParams"] );
				}
				if ($where === false) {
					$this->response->setStatusCode ( 400, "Bad Request" );
					return $this->response;
				}
				
				// append ORDER BY string
				if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
					$builder->orderBy ( $order );
				}
				
				// paginate builder
				$paginator = new PQB ( array (
						"builder" => $builder,
						"limit" => $page_size,
						"page" => $page_number 
				) );
				$page = $paginator->getPaginate ();
				
				// expand & select result set
				$items = array ();
				$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
						"scheme" 
				) );
				foreach ( $page->items as $item ) {
					if (in_array ( "scheme", $expandable )) {
						// expanded scheme
						$items [] = array (
								MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s", Common::API_VERSION_V1, $item->id ), array (
										MG::KEY_ID => $item->id,
										"tsCreate" => $item->tsCreate,
										"tsUpdate" => $item->tsUpdate 
								) ),
								"name" => $item->name,
								"description" => $item->description,
								"lock" => $item->lock,
								"version" => $item->version 
						);
					} else {
						// meta link to the current scheme
						$items [] = array (
								MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s", Common::API_VERSION_V1, $item->id ), array (
										MG::KEY_ID => $item->id 
								) ) 
						);
					}
				}
				
				if (! empty ( $items )) {
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( CG::generate ( $items, $this->request->getURI (), $page->total_items, $page->total_pages, $page->current, $page_size, array (
							"first" => Paginator::DEFAULT_PAGE,
							"previous" => $page->before,
							"next" => $page->next,
							"last" => $page->last 
					) ) );
				} else {
					$this->response->setStatusCode ( 204, "No Content" );
				}
				return $this->response;
				break;
		}
	}
	public function createItemAction() {
	}
	public function readItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$scheme = Scheme::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($scheme) {
					$r = IG::generate ( $this->request->getURI (), array (
							MG::KEY_ID => $scheme->id,
							"tsCreate" => $scheme->tsCreate,
							"tsUpdate" => $scheme->tsUpdate 
					), array (
							"name" => $scheme->name,
							"description" => $scheme->description,
							"lock" => $scheme->lock,
							"version" => $scheme->version 
					) );
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( $r );
				}
				return $this->response;
				break;
		}
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