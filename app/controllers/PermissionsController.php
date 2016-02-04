<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;
use CpdnAPI\Models\Network\Permission;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\IdentityProvider\Profile;
use CpdnAPI\Utils\CollectionGenerator as CG;
use CpdnAPI\Utils\ItemGenerator as IG;
use CpdnAPI\Utils\MetaGenerator as MG;
use CpdnAPI\Utils\ResponseGenerator as RG;
use CpdnAPI\Utils\Searchable;
use CpdnAPI\Utils\Sortable;
use CpdnAPI\Utils\Expandable;
use CpdnAPI\Utils\Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PQB;

class PermissionsController extends ControllerBase {
	
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"tsFrom" => Common::PATTERN_TIMESTAMP,
			"tsTo" => Common::PATTERN_TIMESTAMP,
			"schemeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"profileId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"mode" => "/^rw?x?$/"
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Network\Permission' );
				
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
				return $this->response;
				break;
		}
	}
	public function createItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
		
				if(		preg_match ( $this->validFields ["profileId"], $body->user ) === 1 &&
						preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1 &&
						preg_match ( $this->validFields ["tsFrom"], $body->tsFrom ) === 1 &&
						preg_match ( $this->validFields ["tsTo"], $body->tsTo ) === 1 &&
						preg_match ( $this->validFields ["mode"], $body->mode ) === 1){
								
							$scheme = Scheme::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->scheme
									)
							) );
							
							$profile = Profile::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->user
									)
							) );
								
							// required scheme and user
							if($scheme && $profile){
								$perm = new Permission();
								$perm->mode = $body->mode;
								$perm->profileId = $body->user;
								$perm->schemeId = $body->scheme;
								$perm->tsFrom = $body->tsFrom;
								$perm->tsTo = $body->tsTo;
								
								if($perm->save()){
									$this->response->setStatusCode ( 201, "Created" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $perm->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Scheme or User was not found by posted ids.") );
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
				
				$perm = Permission::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($perm) {
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"scheme", "user"
					) );
					
					$t = Permission::getPermission($perm);
					
					if (in_array ( "scheme", $expandable )) {
						$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $perm->scheme->id ), array (
								MG::KEY_ID => $perm->scheme->id,
								"tsCreate" => $perm->scheme->tsCreate,
								"tsUpdate" => $perm->scheme->tsUpdate
						), Scheme::getScheme ( $perm->scheme ) );
					} 
					
					if (in_array ( "user", $expandable )) {
						$t ["user"] = IG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $perm->profile->id ), array (
								MG::KEY_ID => $perm->profile->id,
								"tsCreate" => $perm->profile->tsCreate,
								"tsUpdate" => $perm->profile->tsUpdate
						), Profile::getProfile ( $perm->profile ) );
					}
					
					$r = IG::generate ( sprintf ( "/%s/permissions/%s/", Common::API_VERSION_V1, $perm->id ), array (
							MG::KEY_ID => $perm->id,
							"tsCreate" => $perm->tsCreate,
							"tsUpdate" => $perm->tsUpdate 
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
				if(		preg_match ( $this->validFields ["profileId"], $body->user ) === 1 &&
						preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1 &&
						preg_match ( $this->validFields ["tsFrom"], $body->tsFrom ) === 1 &&
						preg_match ( $this->validFields ["tsTo"], $body->tsTo ) === 1 &&
						preg_match ( $this->validFields ["mode"], $body->mode ) === 1){
							$perm = Permission::findFirst ( array (
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
							
							$profile = Profile::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->user
									)
							) );
								
							// required scheme and user
							if($perm && $scheme && $profile){
								$perm->mode = $body->mode;
								$perm->profileId = $body->user;
								$perm->schemeId = $body->scheme;
								$perm->tsFrom = $body->tsFrom;
								$perm->tsTo = $body->tsTo;
								
								if($perm->save()){
									$this->response->setStatusCode ( 200, "OK" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $perm->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Permission or Scheme or User was not found by posted ids.") );
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
				
				$perm = Permission::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if($perm){
					if($perm->delete()){
						$this->response->setStatusCode ( 200, "OK" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, ""));
					} else {
						$this->response->setStatusCode ( 500, "Internal Server Error" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Permission was not found by posted id.") );
				}
				return $this->response;
				break;
		}
	}
}