<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;
use CpdnAPI\Models\Network\Section;
use CpdnAPI\Models\Network\MapPoint;
use CpdnAPI\Models\Network\Path;
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

class PathsController extends ControllerBase {
	
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"sectionId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"srcMapPointId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"dstMapPointId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Network\Path' );
				
				// append WHERE conditions
				$where = Searchable::buildQueryBuilderWhereParams ( $this->request->get ( Searchable::URL_QUERY_PARAM ), $this->validFields, array("sectionId") );
				if (is_array ( $where ) && ! empty ( $where ) && $where !== false && $where !== null) {
					$builder->where ( $where ["conditions"], $where ["bindParams"] );
				}
				if ($where === false) {
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_MISSING_FIELD));
					return $this->response;
				}
				
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $where["bindParams"]["sectionId"]
						)
				) );
				
				// acl
				if($section && $this->acl->check($section->schemeId, Permission::MODE_READ) !== true){
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
						"path", "path.section", "path.mapPoint.dst", "path.mapPoint.src"
				) );
				foreach ( $page->items as $item ) {
					if (in_array ( "path", $expandable )) {
						// expanded path
						
						$i = Path::findFirst ( array (
								"id = :id:",
								"bind" => array (
										"id" => $item->id
								)
						) );
						$t = Path::getPath($i);
						
						if (in_array ( "path.section", $expandable )) {
							$t ["section"] = IG::generate ( sprintf ( "/%s/sections/%s/", Common::API_VERSION_V1, $i->section->id ), array (
									MG::KEY_ID => $i->section->id,
									"tsCreate" => $i->section->tsCreate,
									"tsUpdate" => $i->section->tsUpdate
							), Section::getSection( $i->section ) );
						} 
						
						if (in_array ( "path.mapPoint.dst", $expandable )) {
							$t ["mapPoint"]["dst"] = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $i->dstMapPoint->id ), array (
									MG::KEY_ID => $i->dstMapPoint->id,
									"tsCreate" => $i->dstMapPoint->tsCreate,
									"tsUpdate" => $i->dstMapPoint->tsUpdate
							), MapPoint::getMapPoint( $i->dstMapPoint ) );
						}
						
						if (in_array ( "path.mapPoint.src", $expandable )) {
							$t ["mapPoint"]["src"] = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $i->srcMapPoint->id ), array (
									MG::KEY_ID => $i->srcMapPoint->id,
									"tsCreate" => $i->srcMapPoint->tsCreate,
									"tsUpdate" => $i->srcMapPoint->tsUpdate
							), MapPoint::getMapPoint( $i->srcMapPoint ) );
						}
							
						$items [] = IG::generate ( sprintf ( "/%s/paths/%s/", Common::API_VERSION_V1, $i->id ), array (
								MG::KEY_ID => $i->id,
								"tsCreate" => $i->tsCreate,
								"tsUpdate" => $i->tsUpdate
						), $t );
					} else {
						// meta link to the path
						$items [] = array (
								MG::KEY_META => MG::generate ( sprintf ( "/%s/paths/%s/", Common::API_VERSION_V1, $item->id ), array (
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
		
				if(		preg_match ( $this->validFields ["dstMapPointId"], $body->mapPoint->dst ) === 1 &&
						preg_match ( $this->validFields ["srcMapPointId"], $body->mapPoint->src ) === 1 &&
						preg_match ( $this->validFields ["sectionId"], $body->section ) === 1){
		
							$section = Section::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->section 
									) 
							) );
							
							$point_dst = MapPoint::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->mapPoint->dst
									)
							) );
							
							$point_src = MapPoint::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->mapPoint->src
									)
							) );
		
							// required section, point_dst and point_src
							if($section && $point_dst && $point_src){
								if($point_dst->id == $point_src->id){
									$this->response->setStatusCode ( 400, "Bad Request" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Destination and source MapPoint can not be same."));
									return $this->response;
								}
								
								if($point_dst->schemeId != $section->schemeId || $point_src->schemeId != $section->schemeId){
									$this->response->setStatusCode ( 400, "Bad Request" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Destination or source MapPoint's scheme is not same as Section's scheme. In this implementation they must be same."));
									return $this->response;
								}
								
								if($this->acl->check($section->schemeId, Permission::MODE_WRITE) !== true){
									$this->response->setStatusCode ( 403, "Forbidden" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
									return $this->response;
								}
								
								$path = new Path();
								$path->dstMapPointId = $body->mapPoint->dst;
								$path->srcMapPointId = $body->mapPoint->src;
								$path->sectionId = $body->section;
								
								if($path->save()){
									$this->response->setStatusCode ( 201, "Created" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $path->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Section or MapPoint (dst,src) was not found by posted ids.") );
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
				
				$path = Path::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($path) {
					if($this->acl->check($path->section->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"section", "mapPoint.dst", "mapPoint.src"
					) );
					
					$t = Path::getPath($path);
										
					if (in_array ( "section", $expandable )) {
						$t ["section"] = IG::generate ( sprintf ( "/%s/sections/%s/", Common::API_VERSION_V1, $path->section->id ), array (
								MG::KEY_ID => $path->section->id,
								"tsCreate" => $path->section->tsCreate,
								"tsUpdate" => $path->section->tsUpdate
						), Section::getSection( $path->section ) );
					} 
						
					if (in_array ( "mapPoint.dst", $expandable )) {
						$t ["mapPoint"]["dst"] = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $path->dstMapPoint->id ), array (
								MG::KEY_ID => $path->dstMapPoint->id,
								"tsCreate" => $path->dstMapPoint->tsCreate,
								"tsUpdate" => $path->dstMapPoint->tsUpdate
						), MapPoint::getMapPoint( $path->dstMapPoint ) );
					}
						
					if (in_array ( "mapPoint.src", $expandable )) {
						$t ["mapPoint"]["src"] = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $path->srcMapPoint->id ), array (
								MG::KEY_ID => $path->srcMapPoint->id,
								"tsCreate" => $path->srcMapPoint->tsCreate,
								"tsUpdate" => $path->srcMapPoint->tsUpdate
						), MapPoint::getMapPoint( $path->srcMapPoint ) );
					}
					
					$r = IG::generate ( sprintf ( "/%s/paths/%s/", Common::API_VERSION_V1, $path->id ), array (
							MG::KEY_ID => $path->id,
							"tsCreate" => $path->tsCreate,
							"tsUpdate" => $path->tsUpdate 
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
				if(		preg_match ( $this->validFields ["dstMapPointId"], $body->mapPoint->dst ) === 1 &&
						preg_match ( $this->validFields ["srcMapPointId"], $body->mapPoint->src ) === 1 &&
						preg_match ( $this->validFields ["sectionId"], $body->section ) === 1){
		
							$path = Path::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $id 
									) 
							) );
							
							$section = Section::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->section 
									) 
							) );
							
							$point_dst = MapPoint::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->mapPoint->dst
									)
							) );
							
							$point_src = MapPoint::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->mapPoint->src
									)
							) );
		
							// required section, point_dst and point_src
							if($section && $point_dst && $point_src){
								if($point_dst->id == $point_src->id){
									$this->response->setStatusCode ( 400, "Bad Request" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Destination and source MapPoint can not be same."));
									return $this->response;
								}
								
								if($point_dst->schemeId != $section->schemeId || $point_src->schemeId != $section->schemeId){
									$this->response->setStatusCode ( 400, "Bad Request" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Destination or source MapPoint's scheme is not same as Section's scheme. In this implementation they must be same."));
									return $this->response;
								}
								
								if($this->acl->check($section->schemeId, Permission::MODE_WRITE) !== true){
									$this->response->setStatusCode ( 403, "Forbidden" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
									return $this->response;
								}
								
								$path->dstMapPointId = $body->mapPoint->dst;
								$path->srcMapPointId = $body->mapPoint->src;
								$path->sectionId = $body->section;
								
								if($path->save()){
									$this->response->setStatusCode ( 200, "OK" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $path->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Section or MapPoint (dst,src) was not found by posted ids.") );
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
				
				$path = Path::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id
						)
				) );
				
				if($path && $path->section){
					if($this->acl->check($path->section->schemeId, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					if($path->delete()){
						$this->response->setStatusCode ( 200, "OK" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, ""));
					} else {
						$this->response->setStatusCode ( 500, "Internal Server Error" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Path was not found by posted id.") );
				}
				return $this->response;
				break;
		}
	}
}