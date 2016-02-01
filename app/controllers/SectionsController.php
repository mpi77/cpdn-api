<?php

namespace CpdnAPI\Controllers;

use CpdnAPI\Models\Network\Section;
use CpdnAPI\Models\Network\SectionCalc;
use CpdnAPI\Models\Network\SectionSpec;
use CpdnAPI\Models\Network\SectionNode;
use CpdnAPI\Models\Network\Scheme;
use CpdnAPI\Models\Network\Node;
use CpdnAPI\Models\Network\MapPoint;
use CpdnAPI\Models\Network\Path;
use CpdnAPI\Models\Network\Permission;
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

class SectionsController extends ControllerBase {
	
	private $validFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"sectionCalcId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"sectionSpecId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"sectionNodeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"schemeId" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO
	);
	
	private $validCalcFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"currentSrcValue" => Common::PATTERN_DOUBLE_OR_NULL,
			"currentSrcPhase" => Common::PATTERN_DOUBLE_OR_NULL,
			"currentSrcRatio" => Common::PATTERN_DOUBLE_OR_NULL,
			"currentDstValue" => Common::PATTERN_DOUBLE_OR_NULL,
			"currentDstPhase" => Common::PATTERN_DOUBLE_OR_NULL,
			"currentDstRatio" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerSrcActive" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerSrcReactive" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerDstActive" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerDstReactive" => Common::PATTERN_DOUBLE_OR_NULL,
			"lossesActive" => Common::PATTERN_DOUBLE_OR_NULL,
			"lossesReactive" => Common::PATTERN_DOUBLE_OR_NULL
	);
	
	private $validSpecFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"type" => "/^(line|transformer|transformerW3|reactor|switch)$/",
			"tsCreate" => Common::PATTERN_TIMESTAMP,
			"tsUpdate" => Common::PATTERN_TIMESTAMP,
			"status" => "/^$|^(on|off)$/",
			"resistanceValue" => Common::PATTERN_DOUBLE_OR_NULL,
			"resistanceRatio" => Common::PATTERN_DOUBLE_OR_NULL,
			"reactanceValue" => Common::PATTERN_DOUBLE_OR_NULL,
			"reactanceRatio" => Common::PATTERN_DOUBLE_OR_NULL,
			"conductance" => Common::PATTERN_DOUBLE_OR_NULL,
			"susceptance" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltagePriActual" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltagePriRated" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageSecActual" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageSecRated" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageTrcActual" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageTrcRated" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageShortAb" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageShortAc" => Common::PATTERN_DOUBLE_OR_NULL,
			"voltageShortBc" => Common::PATTERN_DOUBLE_OR_NULL,
			"lossesShortAb" => Common::PATTERN_DOUBLE_OR_NULL,
			"lossesShortAc" => Common::PATTERN_DOUBLE_OR_NULL,
			"lossesShortBc" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerRatedAb" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerRatedAc" => Common::PATTERN_DOUBLE_OR_NULL,
			"powerRatedBc" => Common::PATTERN_DOUBLE_OR_NULL,
			"currentNoload" => Common::PATTERN_DOUBLE_OR_NULL,
			"lossesNoload" => Common::PATTERN_DOUBLE_OR_NULL,
			"currentMax" => Common::PATTERN_DOUBLE_OR_NULL
	);
	
	private $validNodesFields = array (
			"id" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"tsCreate" => Common::PATTERN_ISO_8601,
			"tsUpdate" => Common::PATTERN_ISO_8601,
			"dst" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"src" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO,
			"trc" => Common::PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO_OR_NULL
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
				$builder = $this->modelsManager->createBuilder ()->columns ( array_keys ( $this->validFields ) )->from ( 'CpdnAPI\Models\Network\Section' );
				
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
				return $this->response;
				break;
		}
	}
	public function createItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				
				if(		preg_match ( $this->validCalcFields ["currentDstPhase"], $body->calc->current->dst->phase ) === 1 &&
						preg_match ( $this->validCalcFields ["currentDstRatio"], $body->calc->current->dst->ratio ) === 1 &&
						preg_match ( $this->validCalcFields ["currentDstValue"], $body->calc->current->dst->value ) === 1 &&
						preg_match ( $this->validCalcFields ["currentSrcPhase"], $body->calc->current->src->phase ) === 1 &&
						preg_match ( $this->validCalcFields ["currentSrcRatio"], $body->calc->current->src->ratio ) === 1 &&
						preg_match ( $this->validCalcFields ["currentSrcValue"], $body->calc->current->src->value ) === 1 &&
						preg_match ( $this->validCalcFields ["lossesActive"], $body->calc->losses->active ) === 1 &&
						preg_match ( $this->validCalcFields ["lossesReactive"], $body->calc->losses->reactive ) === 1 &&
						preg_match ( $this->validCalcFields ["powerDstActive"], $body->calc->power->dst->active ) === 1 &&
						preg_match ( $this->validCalcFields ["powerDstReactive"], $body->calc->power->dst->reactive ) === 1 &&
						preg_match ( $this->validCalcFields ["powerSrcActive"], $body->calc->power->src->active ) === 1 &&
						preg_match ( $this->validCalcFields ["powerSrcReactive"], $body->calc->power->src->reactive ) === 1 &&
						preg_match ( $this->validSpecFields ["type"], $body->spec->type ) === 1 &&
						preg_match ( $this->validSpecFields ["status"], $body->spec->status ) === 1 &&
						preg_match ( $this->validSpecFields ["resistanceValue"], $body->spec->resistance->value ) === 1 &&
						preg_match ( $this->validSpecFields ["resistanceRatio"], $body->spec->resistance->ratio ) === 1 && 
						preg_match ( $this->validSpecFields ["reactanceValue"], $body->spec->reactance->value ) === 1 &&
						preg_match ( $this->validSpecFields ["reactanceRatio"], $body->spec->reactance->ratio ) === 1 &&
						preg_match ( $this->validSpecFields ["conductance"], $body->spec->conductance ) === 1 &&
						preg_match ( $this->validSpecFields ["susceptance"], $body->spec->susceptance ) === 1 &&
						preg_match ( $this->validSpecFields ["voltagePriActual"], $body->spec->voltage->pri->actual ) === 1 &&
						preg_match ( $this->validSpecFields ["voltagePriRated"], $body->spec->voltage->pri->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageSecActual"], $body->spec->voltage->sec->actual ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageSecRated"], $body->spec->voltage->sec->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageTrcActual"], $body->spec->voltage->trc->actual ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageTrcRated"], $body->spec->voltage->trc->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageShortAb"], $body->spec->voltage->short->ab ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageShortAc"], $body->spec->voltage->short->ac ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageShortBc"], $body->spec->voltage->short->bc ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesShortAb"], $body->spec->losses->short->ab ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesShortAc"], $body->spec->losses->short->ac ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesShortBc"], $body->spec->losses->short->bc ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRatedAb"], $body->spec->power->rated->ab ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRatedAc"], $body->spec->power->rated->ac ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRatedBc"], $body->spec->power->rated->bc ) === 1 &&
						preg_match ( $this->validSpecFields ["currentNoload"], $body->spec->current->noLoad ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesNoload"], $body->spec->losses->noLoad ) === 1 &&
						preg_match ( $this->validSpecFields ["currentMax"], $body->spec->current->max ) === 1 &&
						preg_match ( $this->validNodesFields ["dst"], $body->nodes->dst ) === 1 &&
						preg_match ( $this->validNodesFields ["src"], $body->nodes->src ) === 1 &&
						preg_match ( $this->validNodesFields ["trc"], $body->nodes->trc ) === 1 &&
						preg_match ( $this->validFields ["schemeId"], $body->scheme ) === 1){
					
					$scheme = Scheme::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $body->scheme
							)
					) );
								
					$node_dst = Node::findFirst ( array (
							"id = :id:",
							"bind" => array (
								"id" => $body->nodes->dst
							)
					) );
					
					$node_src = Node::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $body->nodes->src
							)
					) );
					
					$node_trc = null;
					if(!empty($body->nodes->trc)){
						$node_trc = Node::findFirst ( array (
								"id = :id:",
								"bind" => array (
										"id" => $body->nodes->trc
								)
						) );
						if($node_trc && $node_trc->schemeId != $scheme->id){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of selected tercial Node is not same as posted Scheme."));
							return $this->response;
						}
					}
					if(empty($body->nodes->trc)){
						$body->nodes->trc = null;
					}		
					
					// required scheme, node_dst, node_src and optional node_trc
					if($scheme && $node_dst && $node_src && (is_null($body->nodes->trc) || (!is_null($body->nodes->trc) && $node_trc))){
						if($node_dst->schemeId != $scheme->id || $node_src->schemeId != $scheme->id){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of destination or source Node is not same as posted Scheme."));
							return $this->response;
						}
						
						if($node_dst->id == $node_src->id || (!is_null($body->nodes->trc) && $node_dst->id == $node_trc->id) || (!is_null($body->nodes->trc) && $node_src->id == $node_trc->id)){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Destination, source and tercial Nodes can not be same."));
							return $this->response;
						}
						
						if($this->acl->check($scheme->id, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$calc = new SectionCalc();
						$calc->currentDstPhase = $body->calc->current->dst->phase;
						$calc->currentDstRatio = $body->calc->current->dst->ratio;
						$calc->currentDstValue = $body->calc->current->dst->value;
						$calc->currentSrcPhase = $body->calc->current->src->phase;
						$calc->currentSrcRatio = $body->calc->current->src->ratio;
						$calc->currentSrcValue = $body->calc->current->src->value;
						$calc->lossesActive = $body->calc->losses->active;
						$calc->lossesReactive = $body->calc->losses->reactive;
						$calc->powerDstActive = $body->calc->power->dst->active;
						$calc->powerDstReactive = $body->calc->power->dst->reactive;
						$calc->powerSrcActive = $body->calc->power->src->active;
						$calc->powerSrcReactive = $body->calc->power->src->reactive;
						
						$spec = new SectionSpec();
						$spec->type = $body->spec->type;
						$spec->status = $body->spec->status;
						$spec->resistanceValue = $body->spec->resistance->value;
						$spec->resistanceRatio = $body->spec->resistance->ratio;
						$spec->reactanceValue = $body->spec->reactance->value;
						$spec->reactanceRatio = $body->spec->reactance->ratio;
						$spec->conductance = $body->spec->conductance;
						$spec->susceptance = $body->spec->susceptance;
						$spec->voltagePriActual = $body->spec->voltage->pri->actual;
						$spec->voltagePriRated = $body->spec->voltage->pri->rated;
						$spec->voltageSecActual = $body->spec->voltage->sec->actual;
						$spec->voltageSecRated = $body->spec->voltage->sec->rated;
						$spec->voltageTrcActual = $body->spec->voltage->trc->actual;
						$spec->voltageTrcRated = $body->spec->voltage->trc->rated;
						$spec->voltageShortAb = $body->spec->voltage->short->ab;
						$spec->voltageShortAc = $body->spec->voltage->short->ac;
						$spec->voltageShortBc = $body->spec->voltage->short->bc;
						$spec->lossesShortAb = $body->spec->losses->short->ab;
						$spec->lossesShortAc = $body->spec->losses->short->ac;
						$spec->lossesShortBc = $body->spec->losses->short->bc;
						$spec->powerRatedAb = $body->spec->power->rated->ab;
						$spec->powerRatedAc = $body->spec->power->rated->ac;
						$spec->powerRatedBc = $body->spec->power->rated->bc;
						$spec->currentNoload = $body->spec->current->noLoad;
						$spec->lossesNoload = $body->spec->losses->noLoad;
						$spec->currentMax = $body->spec->current->max;
						
						$nodes = new SectionNode();
						$nodes->nodeDst = $body->nodes->dst;
						$nodes->nodeSrc = $body->nodes->src;
						$nodes->nodeTrc = $body->nodes->trc;
							
						if($calc->save() && $spec->save() && $nodes->save()){
							$section = new Section();
							$section->sectionCalcId = (int)$calc->id;
							$section->sectionSpecId = (int)$spec->id;
							$section->sectionNodeId = (int)$nodes->id;
							$section->schemeId = $body->scheme;
							
							if($section->save()){
								$this->response->setStatusCode ( 201, "Created" );
								$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S201, "", array("id" => $section->id)));
							}else{
								$calc->delete();
								$spec->delete();
								$nodes->delete();
								$this->response->setStatusCode ( 500, "Internal Server Error" );
								$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
							}
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Scheme or Nodes (dst,src,trc) was not found by their posted ids.") );
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
				
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($section) {
					if($this->acl->check($section->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"calc",
							"nodes",
							"scheme",
							"spec" 
					) );
					
					$t = Section::getSection($section);
										
					if (in_array ( "calc", $expandable )) {
						$t ["calc"] = IG::generate ( sprintf ( "/%s/sections/%s/calc", Common::API_VERSION_V1, $section->id ), array (
								MG::KEY_ID => $section->id,
								"tsCreate" => $section->calc->tsCreate,
								"tsUpdate" => $section->calc->tsUpdate 
						), SectionCalc::getCalc ( $section->calc ) );
					} 
					
					if (in_array ( "nodes", $expandable )) {
						$t ["nodes"] = IG::generate ( sprintf ( "/%s/sections/%s/nodes", Common::API_VERSION_V1, $section->id ), array (
								MG::KEY_ID => $section->id,
								"tsCreate" => $section->node->tsCreate,
								"tsUpdate" => $section->node->tsUpdate 
						), SectionNode::getNodes($section->node ) );
					} 
					
					if (in_array ( "scheme", $expandable )) {
						$t ["scheme"] = IG::generate ( sprintf ( "/%s/schemes/%s", Common::API_VERSION_V1, $section->scheme->id ), array (
								MG::KEY_ID => $section->scheme->id,
								"tsCreate" => $section->scheme->tsCreate,
								"tsUpdate" => $section->scheme->tsUpdate 
						), Scheme::getScheme ( $section->scheme ) );
					} 
					
					if (in_array ( "spec", $expandable )) {
						$t ["spec"] = IG::generate ( sprintf ( "/%s/sections/%s/spec", Common::API_VERSION_V1, $section->id ), array (
								MG::KEY_ID => $section->id,
								"tsCreate" => $section->spec->tsCreate,
								"tsUpdate" => $section->spec->tsUpdate 
						), SectionSpec::getSpec ( $section->spec ) );
					} 
					
					$r = IG::generate ( sprintf ( "/%s/sections/%s/", Common::API_VERSION_V1, $section->id ), array (
							MG::KEY_ID => $section->id,
							"tsCreate" => $section->tsCreate,
							"tsUpdate" => $section->tsUpdate 
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
				
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if($section){
					if($this->acl->check($section->schemeId, Permission::MODE_WRITE) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$count_paths = Path::count ( array (
							"sectionId = :sectionId:",
							"bind" => array (
									"sectionId" => $section->id
							)
					) );
					
					if($count_paths == 0){
						$this->networkDb->begin();
						if($section->delete() && $section->calc->delete() && $section->spec->delete() && $section->node->delete()) {
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
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR, "Some Path records are in relation with current Section. Remove these records first."));
					}
				} else{
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Section was not found by posted id.") );
				}
				return $this->response;
				break;
		}
	}
	public function readCalcItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($section) {
					if($this->acl->check($section->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( IG::generate ( sprintf ( "/%s/sections/%s/calc", Common::API_VERSION_V1, $section->id ), array (
							MG::KEY_ID => $section->id,
							"tsCreate" => $section->calc->tsCreate,
							"tsUpdate" => $section->calc->tsUpdate 
					), SectionCalc::getCalc ( $section->calc ) ) );
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
				if(		preg_match ( $this->validCalcFields ["currentDstPhase"], $body->current->dst->phase ) === 1 &&
						preg_match ( $this->validCalcFields ["currentDstRatio"], $body->current->dst->ratio ) === 1 &&
						preg_match ( $this->validCalcFields ["currentDstValue"], $body->current->dst->value ) === 1 &&
						preg_match ( $this->validCalcFields ["currentSrcPhase"], $body->current->src->phase ) === 1 &&
						preg_match ( $this->validCalcFields ["currentSrcRatio"], $body->current->src->ratio ) === 1 &&
						preg_match ( $this->validCalcFields ["currentSrcValue"], $body->current->src->value ) === 1 &&
						preg_match ( $this->validCalcFields ["lossesActive"], $body->losses->active ) === 1 &&
						preg_match ( $this->validCalcFields ["lossesReactive"], $body->losses->reactive ) === 1 &&
						preg_match ( $this->validCalcFields ["powerDstActive"], $body->power->dst->active ) === 1 &&
						preg_match ( $this->validCalcFields ["powerDstReactive"], $body->power->dst->reactive ) === 1 &&
						preg_match ( $this->validCalcFields ["powerSrcActive"], $body->power->src->active ) === 1 &&
						preg_match ( $this->validCalcFields ["powerSrcReactive"], $body->power->src->reactive ) === 1 ){
								
							$section = Section::findFirst ( array (
									"id = :id:",
									"bind" => array (
											"id" => $id
									)
							) );
								
							// required section
							if($section){
								if($this->acl->check($section->schemeId, Permission::MODE_WRITE) !== true){
									$this->response->setStatusCode ( 403, "Forbidden" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
									return $this->response;
								}
								
								$calc = $section->calc;
								$calc->currentDstPhase = $body->current->dst->phase;
								$calc->currentDstRatio = $body->current->dst->ratio;
								$calc->currentDstValue = $body->current->dst->value;
								$calc->currentSrcPhase = $body->current->src->phase;
								$calc->currentSrcRatio = $body->current->src->ratio;
								$calc->currentSrcValue = $body->current->src->value;
								$calc->lossesActive = $body->losses->active;
								$calc->lossesReactive = $body->losses->reactive;
								$calc->powerDstActive = $body->power->dst->active;
								$calc->powerDstReactive = $body->power->dst->reactive;
								$calc->powerSrcActive = $body->power->src->active;
								$calc->powerSrcReactive = $body->power->src->reactive;
		
								if($calc->save()){
									$this->response->setStatusCode ( 200, "OK" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $section->id)));
								} else{
									$this->response->setStatusCode ( 500, "Internal Server Error" );
									$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
								}
							} else{
								$this->response->setStatusCode ( 404, "Not Found" );
								$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Section was not found by posted id.") );
							}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
				}
		
				return $this->response;
				break;
		}
	}
	public function readNodesItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
		
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id
						)
				) );
		
				if ($section) {
					if($this->acl->check($section->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( IG::generate ( sprintf ( "/%s/sections/%s/nodes", Common::API_VERSION_V1, $section->id ), array (
							MG::KEY_ID => $section->id,
							"tsCreate" => $section->node->tsCreate,
							"tsUpdate" => $section->node->tsUpdate
					), SectionNode::getNodes ( $section->node ) ) );
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
	public function updateNodesItemAction() {
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$body = $this->request->getJsonRawBody();
				$id = $this->dispatcher->getParam ( "id" );
				if(		preg_match ( $this->validNodesFields ["dst"], $body->dst ) === 1 &&
						preg_match ( $this->validNodesFields ["src"], $body->src ) === 1 &&
						preg_match ( $this->validNodesFields ["trc"], $body->trc ) === 1 ){
					
					$section = Section::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $id
							)
					) );
								
					$node_dst = Node::findFirst ( array (
							"id = :id:",
							"bind" => array (
								"id" => $body->dst
							)
					) );
					
					$node_src = Node::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $body->src
							)
					) );
					
					$node_trc = null;
					if(!empty($body->trc)){
						$node_trc = Node::findFirst ( array (
								"id = :id:",
								"bind" => array (
										"id" => $body->trc
								)
						) );
						if($node_trc && $node_trc->schemeId != $section->schemeId){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of selected tercial Node is not same as Section scheme."));
							return $this->response;
						}
					}
					if(empty($body->trc)){
						$body->trc = null;
					}		
					
					// required section, node_dst, node_src and optional node_trc
					if($section && $node_dst && $node_src && (is_null($body->trc) || (!is_null($body->trc) && $node_trc))){
						if($node_dst->schemeId != $section->schemeId || $node_src->schemeId != $section->schemeId){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Scheme of destination or source Node is not same as Section scheme."));
							return $this->response;
						}
						
						if($node_dst->id == $node_src->id || (!is_null($body->nodes->trc) && $node_dst->id == $node_trc->id) || (!is_null($body->nodes->trc) && $node_src->id == $node_trc->id)){
							$this->response->setStatusCode ( 400, "Bad Request" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Destination, source and tercial Nodes can not be same."));
							return $this->response;
						}
						
						if($this->acl->check($section->schemeId, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$nodes = $section->node;
						$nodes->nodeDst = $body->dst;
						$nodes->nodeSrc = $body->src;
						$nodes->nodeTrc = $body->trc;
							
						if($nodes->save()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $section->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Section or Nodes (dst,src,trc) was not found by their posted ids.") );
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
				
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($section) {
					if($this->acl->check($section->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					
					$r = array (
							MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $section->scheme->id ), array (
									MG::KEY_ID => $section->scheme->id
							) )
					);
					
					$expandable = Expandable::buildExpandableFields ( $this->request->get ( Expandable::URL_QUERY_PARAM ), array (
							"scheme"
					) );
					
					if (in_array ( "scheme", $expandable )) {
						$r = IG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $section->scheme->id ), array (
								MG::KEY_ID => $section->scheme->id,
								"tsCreate" => $section->scheme->tsCreate,
								"tsUpdate" => $section->scheme->tsUpdate
						), Scheme::getScheme ( $section->scheme ) );
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
				
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id 
						) 
				) );
				
				if ($section) {
					if($this->acl->check($section->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					$this->response->setStatusCode ( 200, "OK" );
					$this->response->setJsonContent ( IG::generate ( sprintf ( "/%s/sections/%s/spec", Common::API_VERSION_V1, $section->id ), array (
							MG::KEY_ID => $section->id,
							"tsCreate" => $section->spec->tsCreate,
							"tsUpdate" => $section->spec->tsUpdate 
					), SectionSpec::getSpec ( $section->spec ) ) );
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
						preg_match ( $this->validSpecFields ["status"], $body->status ) === 1 &&
						preg_match ( $this->validSpecFields ["resistanceValue"], $body->resistance->value ) === 1 &&
						preg_match ( $this->validSpecFields ["resistanceRatio"], $body->resistance->ratio ) === 1 && 
						preg_match ( $this->validSpecFields ["reactanceValue"], $body->reactance->value ) === 1 &&
						preg_match ( $this->validSpecFields ["reactanceRatio"], $body->reactance->ratio ) === 1 &&
						preg_match ( $this->validSpecFields ["conductance"], $body->conductance ) === 1 &&
						preg_match ( $this->validSpecFields ["susceptance"], $body->susceptance ) === 1 &&
						preg_match ( $this->validSpecFields ["voltagePriActual"], $body->voltage->pri->actual ) === 1 &&
						preg_match ( $this->validSpecFields ["voltagePriRated"], $body->voltage->pri->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageSecActual"], $body->voltage->sec->actual ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageSecRated"], $body->voltage->sec->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageTrcActual"], $body->voltage->trc->actual ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageTrcRated"], $body->voltage->trc->rated ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageShortAb"], $body->voltage->short->ab ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageShortAc"], $body->voltage->short->ac ) === 1 &&
						preg_match ( $this->validSpecFields ["voltageShortBc"], $body->voltage->short->bc ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesShortAb"], $body->losses->short->ab ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesShortAc"], $body->losses->short->ac ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesShortBc"], $body->losses->short->bc ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRatedAb"], $body->power->rated->ab ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRatedAc"], $body->power->rated->ac ) === 1 &&
						preg_match ( $this->validSpecFields ["powerRatedBc"], $body->power->rated->bc ) === 1 &&
						preg_match ( $this->validSpecFields ["currentNoload"], $body->current->noLoad ) === 1 &&
						preg_match ( $this->validSpecFields ["lossesNoload"], $body->losses->noLoad ) === 1 &&
						preg_match ( $this->validSpecFields ["currentMax"], $body->current->max ) === 1 ){

					$section = Section::findFirst ( array (
							"id = :id:",
							"bind" => array (
									"id" => $id
							)
					) );
					
					// required section
					if($section){
						if($this->acl->check($section->schemeId, Permission::MODE_WRITE) !== true){
							$this->response->setStatusCode ( 403, "Forbidden" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
							return $this->response;
						}
						
						$spec = $section->spec;
						$spec->type = $body->type;
						$spec->status = $body->status;
						$spec->resistanceValue = $body->resistance->value;
						$spec->resistanceRatio = $body->resistance->ratio;
						$spec->reactanceValue = $body->reactance->value;
						$spec->reactanceRatio = $body->reactance->ratio;
						$spec->conductance = $body->conductance;
						$spec->susceptance = $body->susceptance;
						$spec->voltagePriActual = $body->voltage->pri->actual;
						$spec->voltagePriRated = $body->voltage->pri->rated;
						$spec->voltageSecActual = $body->voltage->sec->actual;
						$spec->voltageSecRated = $body->voltage->sec->rated;
						$spec->voltageTrcActual = $body->voltage->trc->actual;
						$spec->voltageTrcRated = $body->voltage->trc->rated;
						$spec->voltageShortAb = $body->voltage->short->ab;
						$spec->voltageShortAc = $body->voltage->short->ac;
						$spec->voltageShortBc = $body->voltage->short->bc;
						$spec->lossesShortAb = $body->losses->short->ab;
						$spec->lossesShortAc = $body->losses->short->ac;
						$spec->lossesShortBc = $body->losses->short->bc;
						$spec->powerRatedAb = $body->power->rated->ab;
						$spec->powerRatedAc = $body->power->rated->ac;
						$spec->powerRatedBc = $body->power->rated->bc;
						$spec->currentNoload = $body->current->noLoad;
						$spec->lossesNoload = $body->losses->noLoad;
						$spec->currentMax = $body->current->max;
						
						if($spec->save()){
							$this->response->setStatusCode ( 200, "OK" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S200, "", array("id" => $section->id)));
						} else{
							$this->response->setStatusCode ( 500, "Internal Server Error" );
							$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S500_CRUD_ERROR));
						}
					} else{
						$this->response->setStatusCode ( 404, "Not Found" );
						$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404, "Section was not found by posted id.") );
					}
				} else{
					$this->response->setStatusCode ( 400, "Bad Request" );
					$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S400_VALIDATION_FAILED, "Regex validation of posted fields failed."));
				}
				
				return $this->response;
				break;
		}
	}
	public function readPathsCollectionAction() {
		$page_size = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_SIZE, "int", Paginator::DEFAULT_PAGE_SIZE );
		$page_number = ( int ) $this->request->get ( Paginator::URL_QUERY_PARAM_PAGE_NUMBER, "int", Paginator::DEFAULT_PAGE );
		
		switch ($this->dispatcher->getParam ( "version" )) {
			case Common::API_VERSION_V1 :
				$id = $this->dispatcher->getParam ( "id" );
				$section = Section::findFirst ( array (
						"id = :id:",
						"bind" => array (
								"id" => $id
						)
				) );
				
				if ($section) {
					if($this->acl->check($section->schemeId, Permission::MODE_READ) !== true){
						$this->response->setStatusCode ( 403, "Forbidden" );
						$this->response->setJsonContent(RG::generateContent($this->request->getURI (), RG::S403_MODE_RESTRICTION));
						return $this->response;
					}
					$builder = $this->modelsManager->createBuilder ()->columns ( array(
							"id",
							"tsCreate",
							"tsUpdate",
							"sectionId",
							"srcMapPointId",
							"dstMapPointId"
					) )->from ( 'CpdnAPI\Models\Network\Path' );
					$builder->where ( "sectionId = :sectionId:", array("sectionId" => $id) );
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
				} else {
					$this->response->setStatusCode ( 404, "Not Found" );
					$this->response->setJsonContent ( RG::generateContent ( $this->request->getURI (), RG::S404 ) );
				}
				return $this->response;
				break;
		}
	}
}