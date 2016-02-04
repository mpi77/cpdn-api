<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;
use CpdnAPI\Models\Editor\Notification;
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

class NotificationsController extends ControllerBase {
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"tsRead" => Common::PATTERN_TIMESTAMP,
			"profileId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"title" => "/^[\s\S]+$/",
			"content" => Common::PATTERN_TEXT_OPTIONAL
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Editor\Notification' );
				
				// append WHERE conditions
				$where = Searchable::buildQueryBuilderWhereParams ( $this->request->get ( Searchable::URL_QUERY_PARAM ), $this->validFields, array("profileId") );
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
						"notification", "notification.user" 
				) );
				foreach ( $page->items as $item ) {
					if (in_array ( "notification", $expandable )) {
						// expanded notification
						
						$i = Notification::findFirst ( array (
								"id = :id:",
								"bind" => array (
										"id" => $item->id
								)
						) );
						$t = Notification::getNotification($i);
						
						if (in_array ( "notification.user", $expandable )) {
							$t ["user"] = IG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $i->profile->id ), array (
									MG::KEY_ID => $i->profile->id,
									"tsCreate" => $i->profile->tsCreate,
									"tsUpdate" => $i->profile->tsUpdate
							), Profile::getProfile ( $i->profile ) );
						}
							
						$items [] = IG::generate ( sprintf ( "/%s/notifications/%s/", Common::API_VERSION_V1, $i->id ), array (
								MG::KEY_ID => $i->id,
								"tsCreate" => $i->tsCreate,
								"tsUpdate" => $i->tsUpdate
						), $t );
					} else {
						// meta link to the notification
						$items [] = array (
								MG::KEY_META => MG::generate ( sprintf ( "/%s/notifications/%s/", Common::API_VERSION_V1, $item->id ), array (
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
	public function readItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$notification = Notification::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($notification) {
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"user"
					) );
					
					$t = Notification::getNotification($notification);
						
					if (in_array ( "user", $expandable )) {
						$t ["user"] = IG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $notification->profile->id ), array (
								MG::KEY_ID => $notification->profile->id,
								"tsCreate" => $notification->profile->tsCreate,
								"tsUpdate" => $notification->profile->tsUpdate
						), Profile::getProfile ( $notification->profile ) );
					}
					
					$r = IG::generate ( sprintf ( "/%s/notifications/%s/", Common::API_VERSION_V1, $notification->id ), array (
							MG::KEY_ID => $notification->id,
							"tsCreate" => $notification->tsCreate,
							"tsUpdate" => $notification->tsUpdate 
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
				if(	preg_match ( $this->validFields ["tsRead"], $body->tsRead ) === 1){
							$notification = Notification::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $id
									)
							) );
								
							// required notification
							if($notification){
								$notification->tsRead = $body->tsRead;
									
								if($notification->save()){
									$this->response->setStatusCode ( 200, "OK" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $notification->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Notification was not found by posted id.") );
							}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
				}
		
				return $this->response;
				break;
		}
	}
}