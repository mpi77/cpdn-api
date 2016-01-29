<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\Network\Node;
use CpdnAPI\Models\Network\NodeSpec;
use CpdnAPI\Models\Network\NodeCalc;
use CpdnAPI\Models\Network\MapPoint;
use CpdnAPI\Models\Network\Section;
use CpdnAPI\Models\Network\SectionCalc;
use CpdnAPI\Models\Network\SectionNode;
use CpdnAPI\Models\Network\SectionSpec;
use CpdnAPI\Models\Network\Object;
use CpdnAPI\Models\Network\Permission;
use CpdnAPI\Models\IdentityProvider\Profile;
use CpdnAPI\Models\Background\Executor;
use CpdnAPI\Models\Background\Task;
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

class SchemesController extends ControllerBase {
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"description" => Common::PATTERN_TEXT_OPTIONAL,
			"lock" => Common::PATTERN_INT_BOOLEAN,
			"name" => "/^[a-zA-Z0-9_\/\.\-]{1,45}$/",
			"version" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO
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
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_MISSING_FIELD));
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
						$i = Scheme::findFirst ( array (
								"id = :id:",
								"bind" => array (
										"id" => $item->id
								)
						) );
						$items [] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $i->id ), array (
								MG::KEY_ID => $i->id,
								"tsCreate" => $i->tsCreate,
								"tsUpdate" => $i->tsUpdate
						), Scheme::getScheme($i) );
					} else {
						// meta link to the current scheme
						$items [] = array (
								MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $item->id ), array (
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
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S204));
				}
				return $this->response;
				break;
		}
	}
	public function createItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				
				if(preg_match ( $this->validFields ["name"], $body->name ) === 1 && preg_match ( $this->validFields ["description"], $body->description ) === 1 && preg_match ( $this->validFields ["version"], $body->version ) === 1 && preg_match ( $this->validFields ["lock"], $body->lock ) === 1){
					$new = new Scheme();
					$new->name = $body->name;
					$new->description = $body->description;
					$new->version = $body->version;
					$new->lock = $body->lock;
					if($new->save()){
						$this->response->setStatusCode ( 201, "Created" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $new->id)));
					} else{
						$this->response->setStatusCode ( 500, "Internal Server Error" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
					}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
				}
				
				return $this->response;
				break;
		}
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
					if($this->acl->check($scheme->id, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$r = IG::generate ( $this->request->getURI (), array (
							MG::KEY_ID => $scheme->id,
							"tsCreate" => $scheme->tsCreate,
							"tsUpdate" => $scheme->tsUpdate 
					), Scheme::getScheme($scheme) );
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( $r );
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S404));
				}
				return $this->response;
				break;
		}
	}
	public function updateItemAction() {
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
					if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$body = $this->request->getJsonRawBody();
					if(preg_match ( $this->validFields ["name"], $body->name ) === 1 && preg_match ( $this->validFields ["description"], $body->description ) === 1 && preg_match ( $this->validFields ["version"], $body->version ) === 1 && preg_match ( $this->validFields ["lock"], $body->lock ) === 1){
						$scheme->name = $body->name;
						$scheme->description = $body->description;
						$scheme->version = $body->version;
						$scheme->lock = $body->lock;
						if($scheme->save()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $scheme->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 400, "Bad Request" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S404));
				}
		
				return $this->response;
				break;
		}
	}
	public function deleteItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
		
				$scheme = Scheme::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
		
				if($scheme){
					if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$count_nodes = Node::count ( array (
							"schemeId = :schemeId:",
							"bind" => array (
									"schemeId" => $scheme->id
							)
					) );
					
					$count_sections = Section::count ( array (
							"schemeId = :schemeId:",
							"bind" => array (
									"schemeId" => $scheme->id
							)
					) );
					
					$count_objects = Object::count(array(
							"schemeId = :schemeId:",
							"bind"=>array(
									"schemeId" => $scheme->id
							)
					));
					
					$count_points = MapPoint::count ( array (
							"schemeId = :schemeId:",
							"bind" => array (
									"schemeId" => $scheme->id
							)
					) );
						
					if($count_nodes == 0 && $count_sections == 0 && $count_objects == 0 && $count_points == 0){
						$this->networkDb->begin();
						
						// remove permissions
						$perms = Permission::find ( array (
								"schemeId = :schemeId:",
								"bind" => array (
										"schemeId" => $scheme->id
								)
						) );
						$t = true;
						foreach ( $perms as $m ) {
							if ($m->delete () == false) {
								$t = false;
								break;
							}
						}
						
						if($t && $scheme->delete()) {
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, ""));
						} else{
							$this->networkDb->rollback();
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
						$this->networkDb->commit();
					} else{
						$this->response->setStatusCode ( 500, "Internal Server Error" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR, "Some Node or Section or MapPoint or Object or Permission records are in relation with current Scheme. Remove these records first."));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Scheme was not found by posted id.") );
				}
				return $this->response;
				break;
		}
	}
	public function readNodesCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
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
					if($this->acl->check($scheme->id, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$builder = $this->modelsManager->createBuilder ()->columns ( array (
							"id",
							"tsCreate",
							"tsUpdate" ,
							"nodeCalcId" ,
							"nodeSpecId" ,
							"schemeId" ,
							"mapPointId"
					) )->from ( 'CpdnAPI\Models\Network\Node' );
					$builder->where ( "schemeId = :schemeId:", array("schemeId" => $id) );
					if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
						$builder->orderBy ( $order );
					}
					
					$paginator = new PQB ( array (
							"builder" => $builder,
							"limit" => $page_size,
							"page" => $page_number
					) );
					$page = $paginator->getPaginate ();
					
					$items = array ();
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"node", "node.calc", "node.mapPoint", "node.spec", "node.scheme"
					) );
					foreach ( $page->items as $item ) {
						if (in_array ( "node", $expandable )) {
							// expanded node
					
							$i = Node::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $item->id
									)
							) );
							$t = Node::getNode($i);
					
							if (in_array ( "node.calc", $expandable )) {
								$t ["calc"] = IG::generate ( sprintf ( "/%s/nodes/%s/calc", Common::API_VERSION_V1, $i->id ), array (
										MG::KEY_ID => $i->id,
										"tsCreate" => $i->calc->tsCreate,
										"tsUpdate" => $i->calc->tsUpdate
								), NodeCalc::getCalc( $i->calc ) );
							}
					
							if (in_array ( "node.mapPoint", $expandable )) {
								$t ["mapPoint"] = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $i->mapPoint->id ), array (
										MG::KEY_ID => $i->mapPoint->id,
										"tsCreate" => $i->mapPoint->tsCreate,
										"tsUpdate" => $i->mapPoint->tsUpdate
								), MapPoint::getMapPoint( $i->mapPoint ) );
							}
					
							if (in_array ( "node.spec", $expandable )) {
								$t ["spec"] = IG::generate ( sprintf ( "/%s/nodes/%s/spec", Common::API_VERSION_V1, $i->id ), array (
										MG::KEY_ID => $i->id,
										"tsCreate" => $i->spec->tsCreate,
										"tsUpdate" => $i->spec->tsUpdate
								), NodeSpec::getSpec( $i->spec ) );
							}
					
							if (in_array ( "node.scheme", $expandable )) {
								$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $i->scheme->id ), array (
										MG::KEY_ID => $i->scheme->id,
										"tsCreate" => $i->scheme->tsCreate,
										"tsUpdate" => $i->scheme->tsUpdate
								), Scheme::getScheme ( $i->scheme ) );
							}
								
							$items [] = IG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $i->id ), array (
									MG::KEY_ID => $i->id,
									"tsCreate" => $i->tsCreate,
									"tsUpdate" => $i->tsUpdate
							), $t );
						} else {
							// meta link to the node
							$items [] = array (
									MG::KEY_META => MG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $item->id ), array (
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S204));
					}
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
	public function readSectionsCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
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
					if($this->acl->check($scheme->id, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$builder = $this->modelsManager->createBuilder ()->columns ( array(
							"id",
							"tsCreate",
							"tsUpdate",
							"sectionCalcId",
							"sectionSpecId",
							"sectionNodeId",
							"schemeId"
					) )->from ( 'CpdnAPI\Models\Network\Section' );
					$builder->where ( "schemeId = :schemeId:", array("schemeId" => $id) );
					if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
						$builder->orderBy ( $order );
					}
					
					$paginator = new PQB ( array (
							"builder" => $builder,
							"limit" => $page_size,
							"page" => $page_number
					) );
					$page = $paginator->getPaginate ();
					
					// expand & select result set
					$items = array ();
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"section", "section.calc", "section.nodes", "section.spec", "section.scheme"
					) );
					foreach ( $page->items as $item ) {
						if (in_array ( "section", $expandable )) {
							// expanded section
					
							$i = Section::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $item->id
									)
							) );
							$t = Section::getSection($i);
					
							if (in_array ( "section.calc", $expandable )) {
								$t ["calc"] = IG::generate ( sprintf ( "/%s/sections/%s/calc", Common::API_VERSION_V1, $i->id ), array (
										MG::KEY_ID => $i->id,
										"tsCreate" => $i->calc->tsCreate,
										"tsUpdate" => $i->calc->tsUpdate
								), SectionCalc::getCalc( $i->calc ) );
							}
					
							if (in_array ( "section.nodes", $expandable )) {
								$t ["nodes"] = IG::generate ( sprintf ( "/%s/sections/%s/nodes", Common::API_VERSION_V1, $i->id ), array (
										MG::KEY_ID => $i->id,
										"tsCreate" => $i->node->tsCreate,
										"tsUpdate" => $i->node->tsUpdate
								), SectionNode::getNodes( $i->node ) );
							}
					
							if (in_array ( "section.spec", $expandable )) {
								$t ["spec"] = IG::generate ( sprintf ( "/%s/sections/%s/spec", Common::API_VERSION_V1, $i->id ), array (
										MG::KEY_ID => $i->id,
										"tsCreate" => $i->spec->tsCreate,
										"tsUpdate" => $i->spec->tsUpdate
								), SectionSpec::getSpec( $i->spec ) );
							}
					
							if (in_array ( "section.scheme", $expandable )) {
								$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $i->scheme->id ), array (
										MG::KEY_ID => $i->scheme->id,
										"tsCreate" => $i->scheme->tsCreate,
										"tsUpdate" => $i->scheme->tsUpdate
								), Scheme::getScheme ( $i->scheme ) );
							}
								
							$items [] = IG::generate ( sprintf ( "/%s/sections/%s/", Common::API_VERSION_V1, $i->id ), array (
									MG::KEY_ID => $i->id,
									"tsCreate" => $i->tsCreate,
									"tsUpdate" => $i->tsUpdate
							), $t );
						} else {
							// meta link to the section
							$items [] = array (
									MG::KEY_META => MG::generate ( sprintf ( "/%s/sections/%s/", Common::API_VERSION_V1, $item->id ), array (
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S204));
					}	
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
	public function readMapPointsCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
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
					if($this->acl->check($scheme->id, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					$builder = $this->modelsManager->createBuilder ()->columns ( array(
							"id",
							"tsCreate",
							"tsUpdate",
							"schemeId",
							"nodeId",
							"x",
							"y",
							"gpsLatitude",
							"gpsLongitude",
							"gpsAltitude"
					) )->from ( 'CpdnAPI\Models\Network\MapPoint' );
					$builder->where ( "schemeId = :schemeId:", array("schemeId" => $id) );
					if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
						$builder->orderBy ( $order );
					}
					
					$paginator = new PQB ( array (
							"builder" => $builder,
							"limit" => $page_size,
							"page" => $page_number
					) );
					$page = $paginator->getPaginate ();
					
					$items = array ();
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"mapPoint", "mapPoint.scheme", "mapPoint.node"
					) );
					foreach ( $page->items as $item ) {
						if (in_array ( "mapPoint", $expandable )) {
							// expanded mapPoint
					
							$i = MapPoint::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $item->id
									)
							) );
							$t = MapPoint::getMapPoint($i);
					
							// node is optional (=null)
							if (in_array ( "mapPoint.node", $expandable ) && !empty($i->nodeId)) {
								$t ["node"] = IG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $i->node->id ), array (
										MG::KEY_ID => $i->node->id,
										"tsCreate" => $i->node->tsCreate,
										"tsUpdate" => $i->node->tsUpdate
								), Node::getNode ( $i->node ) );
							}
					
							if (in_array ( "mapPoint.scheme", $expandable )) {
								$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $i->scheme->id ), array (
										MG::KEY_ID => $i->scheme->id,
										"tsCreate" => $i->scheme->tsCreate,
										"tsUpdate" => $i->scheme->tsUpdate
								), Scheme::getScheme ( $i->scheme ) );
							}
								
							$items [] = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $i->id ), array (
									MG::KEY_ID => $i->id,
									"tsCreate" => $i->tsCreate,
									"tsUpdate" => $i->tsUpdate
							), $t );
						} else {
							// meta link to the mapPoint
							$items [] = array (
									MG::KEY_META => MG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $item->id ), array (
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S204));
					}
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
	public function readObjectsCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
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
					if($this->acl->check($scheme->id, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					$builder = $this->modelsManager->createBuilder ()->columns ( array(
							"id",
							"tsCreate",
							"tsUpdate",
							"schemeId",
							"name"
					) )->from ( 'CpdnAPI\Models\Network\Object' );
					$builder->where ( "schemeId = :schemeId:", array("schemeId" => $id) );
					if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
						$builder->orderBy ( $order );
					}
					
					$paginator = new PQB ( array (
							"builder" => $builder,
							"limit" => $page_size,
							"page" => $page_number
					) );
					$page = $paginator->getPaginate ();
					
					$items = array ();
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"object", "object.scheme"
					) );
					foreach ( $page->items as $item ) {
						if (in_array ( "object", $expandable )) {
							// expanded object
					
							$i = Object::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $item->id
									)
							) );
							$t = Object::getObject($i);
					
							if (in_array ( "object.scheme", $expandable )) {
								$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $i->scheme->id ), array (
										MG::KEY_ID => $i->scheme->id,
										"tsCreate" => $i->scheme->tsCreate,
										"tsUpdate" => $i->scheme->tsUpdate
								), Scheme::getScheme ( $i->scheme ) );
							}
								
							$items [] = IG::generate ( sprintf ( "/%s/objects/%s/", Common::API_VERSION_V1, $i->id ), array (
									MG::KEY_ID => $i->id,
									"tsCreate" => $i->tsCreate,
									"tsUpdate" => $i->tsUpdate
							), $t );
						} else {
							// meta link to the object
							$items [] = array (
									MG::KEY_META => MG::generate ( sprintf ( "/%s/objects/%s/", Common::API_VERSION_V1, $item->id ), array (
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S204));
					}
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
	public function readPermissionsCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
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
					if($this->acl->check($scheme->id, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					$builder = $this->modelsManager->createBuilder ()->columns ( array(
							"id",
							"tsCreate",
							"tsUpdate",
							"tsFrom",
							"tsTo",
							"schemeId",
							"profileId",
							"mode"
					) )->from ( 'CpdnAPI\Models\Network\Permission' );
					$builder->where ( "schemeId = :schemeId:", array("schemeId" => $id) );
					if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
						$builder->orderBy ( $order );
					}
					
					$paginator = new PQB ( array (
							"builder" => $builder,
							"limit" => $page_size,
							"page" => $page_number
					) );
					$page = $paginator->getPaginate ();
					
					$items = array ();
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"permission", "permission.scheme", "permission.user"
					) );
					foreach ( $page->items as $item ) {
						if (in_array ( "permission", $expandable )) {
							// expanded permission
					
							$i = Permission::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $item->id
									)
							) );
							$t = Permission::getPermission($i);
					
							if (in_array ( "permission.scheme", $expandable )) {
								$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $i->scheme->id ), array (
										MG::KEY_ID => $i->scheme->id,
										"tsCreate" => $i->scheme->tsCreate,
										"tsUpdate" => $i->scheme->tsUpdate
								), Scheme::getScheme ( $i->scheme ) );
							}
					
							if (in_array ( "permission.user", $expandable )) {
								$t ["user"] = IG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $i->profile->id ), array (
										MG::KEY_ID => $i->profile->id,
										"tsCreate" => $i->profile->tsCreate,
										"tsUpdate" => $i->profile->tsUpdate
								), Profile::getProfile ( $i->profile ) );
							}
								
							$items [] = IG::generate ( sprintf ( "/%s/permissions/%s/", Common::API_VERSION_V1, $i->id ), array (
									MG::KEY_ID => $i->id,
									"tsCreate" => $i->tsCreate,
									"tsUpdate" => $i->tsUpdate
							), $t );
						} else {
							// meta link to the permission
							$items [] = array (
									MG::KEY_META => MG::generate ( sprintf ( "/%s/permissions/%s/", Common::API_VERSION_V1, $item->id ), array (
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S204));
					}
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
	public function readTasksCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
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
					if($this->acl->check($scheme->id, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$builder = $this->modelsManager->createBuilder ()->columns ( array(
							"id",
							"tsCreate",
							"tsUpdate",
							"tsReceive",
							"tsExecute",
							"schemeId",
							"executorId",
							"profileId",
							"status",
							"priority",
							"command",
							"result"
					) )->from ( 'CpdnAPI\Models\Background\Task' );
					$builder->where ( "schemeId = :schemeId:", array("schemeId" => $id) );
					if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
						$builder->orderBy ( $order );
					}
					
					$paginator = new PQB ( array (
							"builder" => $builder,
							"limit" => $page_size,
							"page" => $page_number
					) );
					$page = $paginator->getPaginate ();
					
					$items = array ();
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"task", "task.scheme", "task.executor", "task.user"
					) );
					foreach ( $page->items as $item ) {
						if (in_array ( "task", $expandable )) {
							// expanded task
					
							$i = Task::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $item->id
									)
							) );
							$t = Task::getTask($i);
					
							if (in_array ( "task.scheme", $expandable )) {
								$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $i->scheme->id ), array (
										MG::KEY_ID => $i->scheme->id,
										"tsCreate" => $i->scheme->tsCreate,
										"tsUpdate" => $i->scheme->tsUpdate
								), Scheme::getScheme ( $i->scheme ) );
							}
					
							if (in_array ( "task.executor", $expandable )) {
								$t ["executor"] = IG::generate ( sprintf ( "/%s/executors/%s/", Common::API_VERSION_V1, $i->executor->id ), array (
										MG::KEY_ID => $i->executor->id,
										"tsCreate" => $i->executor->tsCreate,
										"tsUpdate" => $i->executor->tsUpdate
								), Executor::getExecutor ( $i->executor ) );
							}
					
							if (in_array ( "task.user", $expandable )) {
								$t ["user"] = IG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $i->profile->id ), array (
										MG::KEY_ID => $i->profile->id,
										"tsCreate" => $i->profile->tsCreate,
										"tsUpdate" => $i->profile->tsUpdate
								), Profile::getProfile ( $i->profile ) );
							}
								
							$items [] = IG::generate ( sprintf ( "/%s/tasks/%s/", Common::API_VERSION_V1, $i->id ), array (
									MG::KEY_ID => $i->id,
									"tsCreate" => $i->tsCreate,
									"tsUpdate" => $i->tsUpdate,
									"tsReceive" => $i->tsReceive,
									"tsExecute" => $i->tsExecute
							), $t );
						} else {
							// meta link to the task
							$items [] = array (
									MG::KEY_META => MG::generate ( sprintf ( "/%s/tasks/%s/", Common::API_VERSION_V1, $item->id ), array (
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S204));
					}
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
}