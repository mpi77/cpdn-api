<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Utils\Common;
use CpdnAPI\Models\Background\Executor;
use CpdnAPI\Models\Background\Task;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\IdentityProvider\Profile;
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

class TasksController extends ControllerBase {
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"tsReceive" => Common::PATTERN_TIMESTAMP,
			"tsExecute" => Common::PATTERN_TIMESTAMP,
			"schemeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"executorId" => "/^[a-zA-Z0-9_\/\.\-]{1,20}$/",
			"profileId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"status" => "/^(preparing|new|working|complete)$/",
			"priority" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"command" => Common::PATTERN_TEXT_OPTIONAL,
			"result" => Common::PATTERN_TEXT_OPTIONAL
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Background\Task' );
				
				// append WHERE conditions
				$where = Searchable::buildQueryBuilderWhereParams ( $this->request->get ( Searchable::URL_QUERY_PARAM ), $this->validFields);
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
				return $this->response;
				break;
		}
	}
	public function readItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
	
				$task = Task::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id
						)
				) );
	
				if ($task) {
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"scheme", "executor", "user"
					) );
						
					$t = Task::getTask($task);
	
					if (in_array ( "scheme", $expandable )) {
						$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $task->scheme->id ), array (
								MG::KEY_ID => $task->scheme->id,
								"tsCreate" => $task->scheme->tsCreate,
								"tsUpdate" => $task->scheme->tsUpdate
						), Scheme::getScheme ( $task->scheme ) );
					}
	
					if (in_array ( "executor", $expandable )) {
						$t ["executor"] = IG::generate ( sprintf ( "/%s/executors/%s/", Common::API_VERSION_V1, $task->executor->id ), array (
								MG::KEY_ID => $task->executor->id,
								"tsCreate" => $task->executor->tsCreate,
								"tsUpdate" => $task->executor->tsUpdate
						), Executor::getExecutor ( $task->executor ) );
					}
	
					if (in_array ( "user", $expandable )) {
						$t ["user"] = IG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $task->profile->id ), array (
								MG::KEY_ID => $task->profile->id,
								"tsCreate" => $task->profile->tsCreate,
								"tsUpdate" => $task->profile->tsUpdate
						), Profile::getProfile ( $task->profile ) );
					}
						
					$r = IG::generate ( sprintf ( "/%s/tasks/%s/", Common::API_VERSION_V1, $task->id ), array (
							MG::KEY_ID => $task->id,
							"tsCreate" => $task->tsCreate,
							"tsUpdate" => $task->tsUpdate,
							"tsReceive" => $task->tsReceive,
							"tsExecute" => $task->tsExecute
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
	public function createItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				
				if(		preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1 &&
						preg_match ( $this->validFields ["executorId"], $body->executor ) === 1 &&
						preg_match ( $this->validFields ["profileId"], $body->user ) === 1 &&
						preg_match ( $this->validFields ["status"], $body->status ) === 1 &&
						preg_match ( $this->validFields ["priority"], $body->priority ) === 1 &&
						preg_match ( $this->validFields ["command"], $body->command ) === 1 &&
						preg_match ( $this->validFields ["result"], $body->result ) === 1){
								
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
							
							$executor = Executor::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->executor
									)
							) );
								
							// required scheme, executor, profile
							if($scheme && $executor && $profile){
								/*
								if($this->acl->check($scheme->id, Permission::MODE_EXECUTE) !== true){
									$this->response->setStatusCode ( 403, "Forbidden" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
									return $this->response;
								}
								*/
								$task = new Task();
								$task->schemeId = $body->scheme;
								$task->executorId = $body->executor;
								$task->profileId = $body->user;
								$task->status = $body->status;
								$task->priority = $body->priority;
								$task->command = $body->command;
								$task->result = $body->result;
								
								if($task->save()){
									$this->response->setStatusCode ( 201, "Created" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $task->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Scheme or Profile or Executor was not found by posted ids.") );
							}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
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
						preg_match ( $this->validFields ["executorId"], $body->executor ) === 1 &&
						preg_match ( $this->validFields ["profileId"], $body->user ) === 1 &&
						preg_match ( $this->validFields ["status"], $body->status ) === 1 &&
						preg_match ( $this->validFields ["priority"], $body->priority ) === 1 &&
						preg_match ( $this->validFields ["command"], $body->command ) === 1 &&
						preg_match ( $this->validFields ["result"], $body->result ) === 1){
		
							$task = Task::findFirst ( array (
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
								
							$executor = Executor::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $body->executor
									)
							) );
		
							// required task, scheme, executor, profile
							if($task && $scheme && $executor && $profile){
								$task->schemeId = $body->scheme;
								$task->executorId = $body->executor;
								$task->profileId = $body->user;
								$task->status = $body->status;
								$task->priority = $body->priority;
								$task->command = $body->command;
								$task->result = $body->result;
		
								if($task->save()){
									$this->response->setStatusCode ( 200, "OK" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $task->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Task or Scheme or Profile or Executor was not found by posted ids.") );
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