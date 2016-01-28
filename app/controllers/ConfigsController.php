<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\CollectionGenerator as CG;
use CpdnAPI\Utils\ItemGenerator as IG;
use CpdnAPI\Utils\MetaGenerator as MG;
use CpdnAPI\Utils\ResponseGenerator as RG;
use CpdnAPI\Utils\Searchable;
use CpdnAPI\Utils\Sortable;
use CpdnAPI\Utils\Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PQB;

class ConfigsController extends ControllerBase {
	private $validFields = array (
			"key" => "/^[a-zA-Z0-9_\.\-]{1,45}$/",
			"value" => Common::PATTERN_TEXT_OPTIONAL
	);
	
	public function initialize() {
		$this->view->disable ();
		$this->response->setContentType ( 'application/json', 'UTF-8' );
		$this->response->setStatusCode ( 404, "Not Found" );
	}
	public function readAppCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$items = array ();
				$model = "";
				switch ($this->dispatcher->getParam ( "app" )) {
					case "editor": 
						$model = 'CpdnAPI\Models\Editor\Config';
						break;
					case "background": 
						$model = 'CpdnAPI\Models\Background\Config';
						break;
					case "idp":
						$model = 'CpdnAPI\Models\IdentityProvider\Config';
						break;
				}
				
				if(!empty($model)){
					$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( $model );
				
					$where = Searchable::buildQueryBuilderWhereParams ( $this->request->get ( Searchable::URL_QUERY_PARAM ), $this->validFields );
					if (is_array ( $where ) && ! empty ( $where ) && $where !== false && $where !== null) {
						$builder->where ( $where ["conditions"], $where ["bindParams"] );
					}
					if ($where === false) {
						$this->response->setStatusCode ( 400, "Bad Request" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_MISSING_FIELD));
						return $this->response;
					}
				
					if (($order = Sortable::buildQueryBuilderOrderByParams ( $this->request->get ( Sortable::URL_QUERY_PARAM ), array_keys ( $this->validFields ) )) !== false) {
						$builder->orderBy ( $order );
					}
				
					$paginator = new PQB ( array (
							"builder" => $builder,
							"limit" => $page_size,
							"page" => $page_number 
					) );
					$page = $paginator->getPaginate ();
				
					foreach ( $page->items as $item ) {
						$item = $model::findFirst ( array (
								"key = :key:",
								"bind" => array (
										"key" => $item->key
								)
						) );
						$items [] = IG::generate ( sprintf ( "/%s/configs/%s/%s/", Common::API_VERSION_V1, $this->dispatcher->getParam ( "app" ), $item->key ), array (
								MG::KEY_ID => $item->key
						), $model::getConfig($item) );
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
	public function readAppItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				
				$key = $this->dispatcher->getParam ( "key" );
				$model = "";
				switch ($this->dispatcher->getParam ( "app" )) {
					case "editor":
						$model = 'CpdnAPI\Models\Editor\Config';
						break;
					case "background":
						$model = 'CpdnAPI\Models\Background\Config';
						break;
					case "idp":
						$model = 'CpdnAPI\Models\IdentityProvider\Config';
						break;
				}
				
				if(!empty($model)){
					$config = $model::findFirst ( array (
							"key = :key:",
							"bind" => array (
									"key" => $key
							)
					) );
					
					if ($config) {
						$r = IG::generate ( sprintf ( "/%s/configs/%s/%s/", Common::API_VERSION_V1, $this->dispatcher->getParam ( "app" ), $config->key ), array (
								MG::KEY_ID => $config->key
						), $model::getConfig($config) );
							
						$this->response->setStatusCode ( 200, "OK" );
						$this->response->setJsonContent ( $r );
					} else {
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
}