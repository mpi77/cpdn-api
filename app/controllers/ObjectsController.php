<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;
use CpdnAPI\Models\Network\Object;
use CpdnAPI\Models\Network\ObjectMember;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\Network\Node;
use CpdnAPI\Models\Network\Permission;
use CpdnAPI\Utils\CollectionGenerator as CG;
use CpdnAPI\Utils\ItemGenerator as IG;
use CpdnAPI\Utils\MetaGenerator as MG;
use CpdnAPI\Utils\ResponseGenerator as RG;
use CpdnAPI\Utils\Searchable;
use CpdnAPI\Utils\Sortable;
use CpdnAPI\Utils\Expandable;
use CpdnAPI\Utils\Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PQB;

class ObjectsController extends ControllerBase {
	
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"schemeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"name" => "/^[a-zA-Z0-9_\/\.\-]{1,45}$/"
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Network\Object' );
				
				// append WHERE conditions
				$where = Searchable::buildQueryBuilderWhereParams ( $this->request->get ( Searchable::URL_QUERY_PARAM ), $this->validFields, array("schemeId") );
				if (is_array ( $where ) && ! empty ( $where ) && $where !== false && $where !== null) {
					$builder->where ( $where ["conditions"], $where ["bindParams"] );
				}
				if ($where === false) {
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_MISSING_FIELD));
					return $this->response;
				}
				
				// acl
				if($this->acl->check($where["bindParams"]["schemeId"], Permission::MODE_READ) !== true){
					$this->response->setStatusCode ( 403, "Forbidden" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
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
				return $this->response;
				break;
		}
	}
	public function createItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
		
				if(preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1 &&
						preg_match ( $this->validFields ["name"], $body->name ) === 1){
								
							$scheme = Scheme::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->scheme
									)
							) );
								
							// required scheme
							if($scheme){
								if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
									$this->response->setStatusCode ( 403, "Forbidden" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
									return $this->response;
								}
								
								$object = new Object();
								$object->name = $body->name;
								$object->schemeId = $body->scheme;
								
								if($object->save()){
									$this->response->setStatusCode ( 201, "Created" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $object->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Scheme was not found by posted id.") );
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
				
				$object= Object::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($object) {
					if($this->acl->check($object->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"scheme"
					) );
					
					$t = Object::getObject($object);
										
					if (in_array ( "scheme", $expandable )) {
						$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $object->scheme->id ), array (
								MG::KEY_ID => $object->scheme->id,
								"tsCreate" => $object->scheme->tsCreate,
								"tsUpdate" => $object->scheme->tsUpdate 
						), Scheme::getScheme( $object->scheme ) );
					} 
					
					
					$r = IG::generate ( sprintf ( "/%s/objects/%s/", Common::API_VERSION_V1, $object->id ), array (
							MG::KEY_ID => $object->id,
							"tsCreate" => $object->tsCreate,
							"tsUpdate" => $object->tsUpdate 
					), $t );
					
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( $r );
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
	public function updateItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				$id = $this->dispatcher->getParam ( "id" );
				if(		preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1 &&
						preg_match ( $this->validFields ["name"], $body->name ) === 1){
								
							$object= Object::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $id
									)
							) );
							
							$scheme = Scheme::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->scheme
									)
							) );
							
							if($object && $scheme && $object->schemeId != $scheme->id){
								$this->response->setStatusCode ( 400, "Bad Request" );
								$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme posted in body request is not same as Object's scheme. In this implementation they must be same."));
								return $this->response;
							}
								
							// required scheme and object
							if($object && $scheme && $object->schemeId == $scheme->id){
								if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
									$this->response->setStatusCode ( 403, "Forbidden" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
									return $this->response;
								}
								
								$object->name = $body->name;
								$object->schemeId = $body->scheme;
								
								if($object->save()){
									$this->response->setStatusCode ( 200, "OK" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $object->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Object or Scheme was not found by posted ids.") );
							}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
				}
		
				return $this->response;
				break;
		}
	}
	public function deleteItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$object= Object::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if($object){
					if($this->acl->check($object->schemeId, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$count_members = ObjectMember::count(array(
							"objectId = :objectId:", 
							"bind"=>array(
									"objectId" => $object->id
							)
					));
					
					if($count_members == 0){
						if($object->delete()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, ""));
						} else {
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 500, "Internal Server Error" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR, "Some ObjectMember records are in relation with current Object. Remove these records first."));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Object was not found by posted id.") );
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
				$objectId = $this->dispatcher->getParam ( "id" );
				
				$object= Object::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $objectId
						)
				) );
				
				if (!$object) {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Object was not found by posted id.") );
					return $this->response;
				}
				
				// acl
				if($this->acl->check($object->schemeId, Permission::MODE_READ) !== true){
					$this->response->setStatusCode ( 403, "Forbidden" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
					return $this->response;
				}
				
				$builder = $this->modelsManager->createBuilder ()->columns ( array ( "objectId", "nodeId" ) )->from ( 'CpdnAPI\Models\Network\ObjectMember' );
				$builder->where("objectId = :objectId:", array("objectId" => $objectId));
				$builder->orderBy("nodeId");
				
				// paginate builder
				$paginator = new PQB ( array (
						"builder" => $builder,
						"limit" => $page_size,
						"page" => $page_number 
				) );
				$page = $paginator->getPaginate ();
				
				// select result set
				$items = array ();
				foreach ( $page->items as $item ) {
					$items [] = array (
							MG::KEY_META => MG::generate ( sprintf ( "/%s/objects/%s/node/%s", Common::API_VERSION_V1, $item->objectId, $item->nodeId ), array (
								"objectId" => $item->objectId,
								"nodeId" => $item->nodeId
						) ) 
					);
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
	public function createNodeItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$objectId = $this->dispatcher->getParam ( "objectId" );
				$nodeId = $this->dispatcher->getParam ( "nodeId" );
				
				$object = Object::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $objectId
						)
				) );
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $nodeId
						)
				) );
				
				if($object && $node){
					if($object->schemeId != $node->schemeId){
						$this->response->setStatusCode ( 400, "Bad Request" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of Object is not same as Node's scheme. In this implementation they must be same."));
						return $this->response;
					}
					if($this->acl->check($object->schemeId, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$member = ObjectMember::findFirst ( array (
							"objectId = :objectId: AND nodeId = :nodeId:",
							"bind" => array (
									"objectId" => $objectId,
									"nodeId" => $nodeId
							)
					) );
					
					if(!$member){
						$om = new ObjectMember();
						$om->objectId = $objectId;
						$om->nodeId = $nodeId;
						
						if($om->save()){
							$this->response->setStatusCode ( 201, "Created" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("objectId" => $objectId, "nodeId" => $nodeId)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 200, "OK" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("objectId" => $objectId, "nodeId" => $nodeId)));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Object or Node was not found by posted ids.") );
				}
				return $this->response;
				break;
		}
	}
	public function deleteNodeItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$objectId = $this->dispatcher->getParam ( "objectId" );
				$nodeId = $this->dispatcher->getParam ( "nodeId" );
				
				$object = Object::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $objectId
						)
				) );
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $nodeId
						)
				) );
				
				if($object && $node){
					if($object->schemeId != $node->schemeId){
						$this->response->setStatusCode ( 400, "Bad Request" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of Object is not same as Node's scheme. In this implementation they must be same."));
						return $this->response;
					}
					if($this->acl->check($object->schemeId, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$member = ObjectMember::findFirst ( array (
							"objectId = :objectId: AND nodeId = :nodeId:",
							"bind" => array (
									"objectId" => $objectId,
									"nodeId" => $nodeId
							)
					) );
					
					if($member){
						if($member->delete()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "ObjectMember was not found by posted objectId and nodeId.") );
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Object or Node was not found by posted ids.") );
				}
				return $this->response;
				break;
		}
	}
}