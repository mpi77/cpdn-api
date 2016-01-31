<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Models\Network\Node;
use CpdnAPI\Models\Network\NodeCalc;
use CpdnAPI\Models\Network\NodeSpec;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\Network\MapPoint;
use CpdnAPI\Models\Network\Permission;
use CpdnAPI\Models\Network\ObjectMember;
use CpdnAPI\Models\Network\SectionNode;
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
		
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"nodeCalcId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"nodeSpecId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"schemeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"mapPointId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO
	);
	
	private $validCalcFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"loadActive" => Common::PATTERN_DOUBLE_OR_NULL,
			"loadReactive" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageDropKv" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageDropProc" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltagePhase" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageValue" => Common::PATTERN_DOUBLE_OR_NULL
	);
	
	private $validSpecFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"type" => "/^(power|consumption|turbogen|hydrogen|superiorSystem)$/",
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"cosFi" => Common::PATTERN_DOUBLE_OR_NULL,
			"mi" => Common::PATTERN_DOUBLE_OR_NULL,
			"lambdaMax" => Common::PATTERN_DOUBLE_OR_NULL,
			"lambdaMin" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerActive" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerInstalled" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerRated" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerReactive" => Common::PATTERN_DOUBLE_OR_NULL,
			"reactanceLongitudinal" => Common::PATTERN_DOUBLE_OR_NULL,
			"reactanceTransverse" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageLevel" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltagePhase" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageRated" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageValue" => Common::PATTERN_DOUBLE_OR_NULL
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Network\Node' );
				
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
				return $this->response;
				break;
		}
	}
	public function createItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				
				if(preg_match ( $this->validCalcFields ["loadActive"], $body->calc->load->active ) === 1 && 
						preg_match ( $this->validCalcFields ["loadReactive"], $body->calc->load->reactive ) === 1 &&
						preg_match ( $this->validCalcFields ["voltageDropKv"], $body->calc->voltage->dropKv ) === 1 &&
						preg_match ( $this->validCalcFields ["voltageDropProc"], $body->calc->voltage->dropProc ) === 1 &&
						preg_match ( $this->validCalcFields ["voltagePhase"], $body->calc->voltage->phase ) === 1 &&
						preg_match ( $this->validCalcFields ["voltageValue"], $body->calc->voltage->value ) === 1 &&
						preg_match ( $this->validSpecFields ["type"], $body->spec->type ) === 1 &&
						preg_match ( $this->validSpecFields ["cosFi"], $body->spec->cosFi ) === 1 &&
						preg_match ( $this->validSpecFields ["mi"], $body->spec->mi ) === 1 &&
						preg_match ( $this->validSpecFields ["lambdaMax"], $body->spec->lambda->max ) === 1 &&
						preg_match ( $this->validSpecFields ["lambdaMin"], $body->spec->lambda->min ) === 1 &&
						preg_match ( $this->validSpecFields ["powerActive"], $body->spec->power->active ) === 1 && 
						preg_match ( $this->validSpecFields ["powerInstalled"], $body->spec->power->installed ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRated"], $body->spec->power->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["powerReactive"], $body->spec->power->reactive ) === 1 &&
						preg_match ( $this->validSpecFields ["reactanceLongitudinal"], $body->spec->reactance->longitudinal ) === 1 &&
						preg_match ( $this->validSpecFields ["reactanceTransverse"], $body->spec->reactance->transverse ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageLevel"], $body->spec->voltage->level ) === 1 &&
						preg_match ( $this->validSpecFields ["voltagePhase"], $body->spec->voltage->phase ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageRated"], $body->spec->voltage->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageValue"], $body->spec->voltage->value ) === 1 &&
						preg_match ( $this->validFields ["mapPointId"], $body->mapPoint ) === 1 && 
						preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1){
					
					$scheme = Scheme::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $body->scheme
							)
					) );
								
					$point = MapPoint::findFirst ( array (
							"id = :id:",
							"bind" => array (
								"id" => $body->mapPoint
							)
					) );
								
					// required scheme and mapPoint
					if($scheme && $point){
						if($point->schemeId != $scheme->id){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of MapPoint is not same as posted Scheme."));
							return $this->response;
						}
						
						if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$calc = new NodeCalc();
						$calc->loadActive = $body->calc->load->active;
						$calc->loadReactive = $body->calc->load->reactive;
						$calc->voltageDropKv = $body->calc->voltage->dropKv;
						$calc->voltageDropProc = $body->calc->voltage->dropProc ;
						$calc->voltagePhase = $body->calc->voltage->phase;
						$calc->voltageValue = $body->calc->voltage->value;
							
						$spec = new NodeSpec();
						$spec->type = $body->spec->type;
						$spec->cosFi = $body->spec->cosFi;
						$spec->mi = $body->spec->mi;
						$spec->lambdaMax = $body->spec->lambda->max;
						$spec->lambdaMin = $body->spec->lambda->min;
						$spec->powerActive = $body->spec->power->active;
						$spec->powerInstalled = $body->spec->power->installed;
						$spec->powerRated = $body->spec->power->rated;
						$spec->powerReactive = $body->spec->power->reactive;
						$spec->reactanceLongitudinal = $body->spec->reactance->longitudinal;
						$spec->reactanceTransverse = $body->spec->reactance->transverse;
						$spec->voltageLevel = $body->spec->voltage->level;
						$spec->voltagePhase = $body->spec->voltage->phase;
						$spec->voltageRated = $body->spec->voltage->rated;
						$spec->voltageValue = $body->spec->voltage->value;
							
						if($calc->save() && $spec->save()){
							$node = new Node();
							$node->nodeCalcId = $calc->id;
							$node->nodeSpecId = $spec->id;
							$node->mapPointId = $body->mapPoint;
							$node->schemeId = $body->scheme;
						
							if($node->save()){
								$this->response->setStatusCode ( 201, "Created" );
								$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $node->id)));
							}else{
								$calc->delete();
								$spec->delete();
								$this->response->setStatusCode ( 500, "Internal Server Error" );
								$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
							}
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Scheme or MapPoint was not found by their posted ids.") );
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
				
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($node) {
					if($this->acl->check($node->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"calc",
							"mapPoint",
							"scheme",
							"spec" 
					) );
					
					$t = Node::getNode($node);
										
					if (in_array ( "calc", $expandable )) {
						$t ["calc"] = IG::generate ( sprintf ( "/%s/nodes/%s/calc", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id,
								"tsCreate" => $node->calc->tsCreate,
								"tsUpdate" => $node->calc->tsUpdate 
						), NodeCalc::getCalc ( $node->calc ) );
					} 
					
					if (in_array ( "mapPoint", $expandable )) {
						$t ["mapPoint"] = IG::generate ( sprintf ( "/%s/mapPoints/%s", Common::API_VERSION_V1, $node->mapPoint->id ), array (
								MG::KEY_ID => $node->mapPoint->id,
								"tsCreate" => $node->mapPoint->tsCreate,
								"tsUpdate" => $node->mapPoint->tsUpdate 
						), MapPoint::getMapPoint ( $node->mapPoint ) );
					} 
					
					if (in_array ( "scheme", $expandable )) {
						$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s", Common::API_VERSION_V1, $node->scheme->id ), array (
								MG::KEY_ID => $node->scheme->id,
								"tsCreate" => $node->scheme->tsCreate,
								"tsUpdate" => $node->scheme->tsUpdate 
						), Scheme::getScheme ( $node->scheme ) );
					} 
					
					if (in_array ( "spec", $expandable )) {
						$t ["spec"] = IG::generate ( sprintf ( "/%s/nodes/%s/spec", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id,
								"tsCreate" => $node->spec->tsCreate,
								"tsUpdate" => $node->spec->tsUpdate 
						), NodeSpec::getSpec ( $node->spec ) );
					} 
					
					$r = IG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $node->id ), array (
							MG::KEY_ID => $node->id,
							"tsCreate" => $node->tsCreate,
							"tsUpdate" => $node->tsUpdate 
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
	public function deleteItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
		
				$node = Node::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
		
				if($node){
					if($this->acl->check($node->schemeId, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$count_members = ObjectMember::count(array(
							"nodeId = :nodeId:",
							"bind"=>array(
									"nodeId" => $node->id
							)
					));
						
					$count_sections = SectionNode::count ( array (
							"nodeSrc = :nodeId: OR nodeDst = :nodeId: OR nodeTrc = :nodeId:",
							"bind" => array (
									"nodeId" => $node->id
							)
					) );
					
					$count_points = MapPoint::count ( array (
							"nodeId = :nodeId:",
							"bind" => array (
									"nodeId" => $node->id
							)
					) );
						
					if($count_members == 0 && $count_sections == 0 && $count_points == 0){
						$this->networkDb->begin();
						if($node->delete() && $node->calc->delete() && $node->spec->delete()) {
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR, "Some ObjectMember or SectionNode or MapPoint records are in relation with current Node. Remove these records first."));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Node was not found by posted id.") );
				}
				return $this->response;
				break;
		}
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
					if($this->acl->check($node->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
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
	}
	public function updateCalcItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				$id = $this->dispatcher->getParam ( "id" );
				if(		preg_match ( $this->validCalcFields ["loadActive"], $body->load->active ) === 1 && 
						preg_match ( $this->validCalcFields ["loadReactive"], $body->load->reactive ) === 1 &&
						preg_match ( $this->validCalcFields ["voltageDropKv"], $body->voltage->dropKv ) === 1 &&
						preg_match ( $this->validCalcFields ["voltageDropProc"], $body->voltage->dropProc ) === 1 &&
						preg_match ( $this->validCalcFields ["voltagePhase"], $body->voltage->phase ) === 1 &&
						preg_match ( $this->validCalcFields ["voltageValue"], $body->voltage->value ) === 1){
					
					$node = Node::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $id
							)
					) );
								
					// required node
					if($node){
						if($this->acl->check($node->schemeId, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$calc = $node->calc;
						$calc->loadActive = $body->load->active;
						$calc->loadReactive = $body->load->reactive;
						$calc->voltageDropKv = $body->voltage->dropKv;
						$calc->voltageDropProc = $body->voltage->dropProc ;
						$calc->voltagePhase = $body->voltage->phase;
						$calc->voltageValue = $body->voltage->value;
						
						if($calc->save()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $node->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Node was not found by posted id.") );
					}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
				}
				
				return $this->response;
				break;
		}
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
					if($this->acl->check($node->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$r = array (
							MG::KEY_META => MG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $node->mapPoint->id ), array (
									MG::KEY_ID => $node->mapPoint->id
							) )
					);
						
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"mapPoint"
					) );
						
					if (in_array ( "mapPoint", $expandable )) {
						$r = IG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $node->mapPoint->id ), array (
								MG::KEY_ID => $node->mapPoint->id,
								"tsCreate" => $node->mapPoint->tsCreate,
								"tsUpdate" => $node->mapPoint->tsUpdate
						), MapPoint::getMapPoint ( $node->mapPoint ) );
					}
						
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
	public function updateMapPointItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				$id = $this->dispatcher->getParam ( "id" );
				if(preg_match ( $this->validFields ["mapPointId"], $body->mapPoint ) === 1){
					
					$node = Node::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $id
							)
					) );
					
					$point = MapPoint::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $body->mapPoint
							)
					) );
								
					// required node, mapPoint
					if($node && $point){
						if($point->schemeId != $node->schemeId){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of MapPoint is not same as Node's scheme."));
							return $this->response;
						}
						if($this->acl->check($node->schemeId, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$node->mapPointId = $body->mapPoint;
						
						if($node->save()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $node->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Node or MapPoint was not found by their posted ids.") );
					}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
				}
				
				return $this->response;
				break;
		}
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
					if($this->acl->check($node->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$r = array (
							MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $node->scheme->id ), array (
									MG::KEY_ID => $node->scheme->id
							) )
					);
					
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"scheme"
					) );
					
					if (in_array ( "scheme", $expandable )) {
						$r = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $node->scheme->id ), array (
								MG::KEY_ID => $node->scheme->id,
								"tsCreate" => $node->scheme->tsCreate,
								"tsUpdate" => $node->scheme->tsUpdate
						), Scheme::getScheme ( $node->scheme ) );
					} 
					
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
					if($this->acl->check($node->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
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
	}
	public function updateSpecItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				$id = $this->dispatcher->getParam ( "id" );
				if(		preg_match ( $this->validSpecFields ["type"], $body->type ) === 1 &&
						preg_match ( $this->validSpecFields ["cosFi"], $body->cosFi ) === 1 &&
						preg_match ( $this->validSpecFields ["mi"], $body->mi ) === 1 &&
						preg_match ( $this->validSpecFields ["lambdaMax"], $body->lambda->max ) === 1 &&
						preg_match ( $this->validSpecFields ["lambdaMin"], $body->lambda->min ) === 1 &&
						preg_match ( $this->validSpecFields ["powerActive"], $body->power->active ) === 1 && 
						preg_match ( $this->validSpecFields ["powerInstalled"], $body->power->installed ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRated"], $body->power->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["powerReactive"], $body->power->reactive ) === 1 &&
						preg_match ( $this->validSpecFields ["reactanceLongitudinal"], $body->reactance->longitudinal ) === 1 &&
						preg_match ( $this->validSpecFields ["reactanceTransverse"], $body->reactance->transverse ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageLevel"], $body->voltage->level ) === 1 &&
						preg_match ( $this->validSpecFields ["voltagePhase"], $body->voltage->phase ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageRated"], $body->voltage->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageValue"], $body->voltage->value ) === 1 ){
					
					$node = Node::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $id
							)
					) );
								
					// required node
					if($node){
						if($this->acl->check($node->schemeId, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$spec = $node->spec;
						$spec->type = $body->type;
						$spec->cosFi = $body->cosFi;
						$spec->mi = $body->mi;
						$spec->lambdaMax = $body->lambda->max;
						$spec->lambdaMin = $body->lambda->min;
						$spec->powerActive = $body->power->active;
						$spec->powerInstalled = $body->power->installed;
						$spec->powerRated = $body->power->rated;
						$spec->powerReactive = $body->power->reactive;
						$spec->reactanceLongitudinal = $body->reactance->longitudinal;
						$spec->reactanceTransverse = $body->reactance->transverse;
						$spec->voltageLevel = $body->voltage->level;
						$spec->voltagePhase = $body->voltage->phase;
						$spec->voltageRated = $body->voltage->rated;
						$spec->voltageValue = $body->voltage->value;
							
						if($spec->save()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $node->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Node was not found by posted id.") );
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