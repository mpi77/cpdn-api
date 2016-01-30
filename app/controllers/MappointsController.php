<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;
use CpdnAPI\Models\Network\MapPoint;
use CpdnAPI\Models\Network\Node;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\Network\Permission;
use CpdnAPI\Models\Network\Path;
use CpdnAPI\Utils\CollectionGenerator as CG;
use CpdnAPI\Utils\ItemGenerator as IG;
use CpdnAPI\Utils\MetaGenerator as MG;
use CpdnAPI\Utils\ResponseGenerator as RG;
use CpdnAPI\Utils\Searchable;
use CpdnAPI\Utils\Sortable;
use CpdnAPI\Utils\Expandable;
use CpdnAPI\Utils\Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PQB;

class MappointsController extends ControllerBase {
	
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"schemeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"nodeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO_OR_NULL,
			"x" => Common::PATTERN_UNSIGNED_INTEGER_WITH_ZERO,
			"y" => Common::PATTERN_UNSIGNED_INTEGER_WITH_ZERO,
			"gpsLatitude" => Common::PATTERN_DOUBLE_OR_NULL,
			"gpsLongitude" => Common::PATTERN_DOUBLE_OR_NULL,
			"gpsAltitude" => Common::PATTERN_DOUBLE_OR_NULL
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Network\MapPoint' );
				
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
				return $this->response;
				break;
		}
	}
	public function createItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				
				if(preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1 && 
						preg_match ( $this->validFields ["nodeId"], $body->node ) === 1 &&
						preg_match ( $this->validFields ["x"], $body->x ) === 1 &&
						preg_match ( $this->validFields ["y"], $body->y ) === 1 &&
						preg_match ( $this->validFields ["gpsLatitude"], $body->gps->latitude ) === 1 &&
						preg_match ( $this->validFields ["gpsLongitude"], $body->gps->longitude ) === 1 &&
						preg_match ( $this->validFields ["gpsAltitude"], $body->gps->altitude ) === 1 ){
					
					$scheme = Scheme::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $body->scheme
							)
					) );
					
					$node = null;
					if(!empty($body->node)){
						$node = Node::findFirst ( array (
								"id = :id:",
								"bind" => array (
										"id" => $body->node
								)
						) );
						if($node && $node->schemeId != $scheme->id){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of selected Node is not same as MapPoint's scheme."));
							return $this->response;
						}
					}
					if(empty($body->node)){
						$body->node = null;
					}
					
					// required scheme, optional node
					if($scheme && (is_null($body->node) || (!is_null($body->node) && $node))){
						if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$point = new MapPoint();
						$point->schemeId = $body->scheme;
						$point->nodeId = $body->node;
						$point->x = $body->x;
						$point->y = $body->y;
						$point->gpsLatitude = $body->gps->latitude;
						$point->gpsLongitude = $body->gps->longitude;
						$point->gpsAltitude = $body->gps->altitude;
							
						if($point->save()){
							$this->response->setStatusCode ( 201, "Created" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $point->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Scheme or Node was not found by their posted ids.") );
					}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED), "Regex validation of posted fields failed.");
				}
				
				return $this->response;
				break;
		}
	}
	public function readItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$point = MapPoint::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($point) {
					if($this->acl->check($point->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"node",
							"scheme"
					) );
					
					$t = MapPoint::getMapPoint($point);
					
					// node is optional (=null)
					if (in_array ( "node", $expandable ) && !empty($point->nodeId)) {
						$t ["node"] = IG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $point->node->id ), array (
								MG::KEY_ID => $point->node->id,
								"tsCreate" => $point->node->tsCreate,
								"tsUpdate" => $point->node->tsUpdate 
						), Node::getNode ( $point->node ) );
					} 
					
					if (in_array ( "scheme", $expandable )) {
						$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $point->scheme->id ), array (
								MG::KEY_ID => $point->scheme->id,
								"tsCreate" => $point->scheme->tsCreate,
								"tsUpdate" => $point->scheme->tsUpdate 
						), Scheme::getScheme ( $point->scheme ) );
					} 
					
					
					$r = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $point->id ), array (
							MG::KEY_ID => $point->id,
							"tsCreate" => $point->tsCreate,
							"tsUpdate" => $point->tsUpdate 
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
						preg_match ( $this->validFields ["nodeId"], $body->node ) === 1 &&
						preg_match ( $this->validFields ["x"], $body->x ) === 1 &&
						preg_match ( $this->validFields ["y"], $body->y ) === 1 &&
						preg_match ( $this->validFields ["gpsLatitude"], $body->gps->latitude ) === 1 &&
						preg_match ( $this->validFields ["gpsLongitude"], $body->gps->longitude ) === 1 &&
						preg_match ( $this->validFields ["gpsAltitude"], $body->gps->altitude ) === 1 ){
					
					$point = MapPoint::findFirst ( array (
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
					
					if($point && $scheme && $point->schemeId != $scheme->id){
						$this->response->setStatusCode ( 400, "Bad Request" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme posted in body request is not same as MapPoint's scheme. In this implementation they must be same."));
						return $this->response;
					}
					
					$node = null;
					if(!empty($body->node)){
						$node = Node::findFirst ( array (
								"id = :id:",
								"bind" => array (
										"id" => $body->node
								)
						) );
						if($node && $node->schemeId != $scheme->id){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of selected Node is not same as MapPoint's scheme."));
							return $this->response;
						}
					}
					if(empty($body->node)){
						$body->node = null;
					}
					
					// required scheme, optional node
					// disabled migration to other scheme
					if($point && $scheme && $point->schemeId == $scheme->id && (is_null($body->node) || (!is_null($body->node) && $node))){
						if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						$point->schemeId = $body->scheme;
						$point->nodeId = $body->node;
						$point->x = $body->x;
						$point->y = $body->y;
						$point->gpsLatitude = $body->gps->latitude;
						$point->gpsLongitude = $body->gps->longitude;
						$point->gpsAltitude = $body->gps->altitude;
							
						if($point->save()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $point->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "MapPoint or Scheme or Node was not found by posted ids.") );
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
				
				$point = MapPoint::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if($point){
					if($this->acl->check($point->schemeId, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$count_paths = Path::count ( array (
							"srcMapPointId = :pointId: OR dstMapPointId = :pointId:",
							"bind" => array (
									"pointId" => $point->id
							)
					) );
					
					$count_nodes = Node::count ( array (
							"mapPointId = :pointId:",
							"bind" => array (
									"pointId" => $point->id
							)
					) );
					
					if($count_paths == 0 && $count_nodes == 0){
						if($point->delete()) {
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, ""));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 500, "Internal Server Error" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR, "Some Path or Node records are in relation with current MapPoint. Remove these records first."));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "MapPoint was not found by posted id.") );
				}
				return $this->response;
				break;
		}
	}
}