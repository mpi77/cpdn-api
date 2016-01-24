<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Node extends Model {
	/**
	 *
	 * @var integer
	 *
	 */
	public $id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeCalcId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeSpecId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $schemeId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $mapPointId;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $tsCreate;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $tsUpdate;
	public function initialize() {
		$this->setConnectionService ( 'networkDb' );
		
		$this->belongsTo ( "mapPointId", "CpdnAPI\Models\Network\MapPoint", "id", array (
				'alias' => 'mapPoint' 
		) );
		$this->belongsTo ( "schemeId", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme' 
		) );
		$this->belongsTo ( "nodeSpecId", "CpdnAPI\Models\Network\NodeSpec", "id", array (
				'alias' => 'spec' 
		) );
		$this->belongsTo ( "nodeCalcId", "CpdnAPI\Models\Network\NodeCalc", "id", array (
				'alias' => 'calc' 
		) );
		
		$this->hasOne ( "id", "CpdnAPI\Models\Network\MapPoint", "nodeId", array (
				'alias' => 'oMapPoint' 
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\ObjectMember", "nodeId", array (
				'alias' => 'nObjectMember' 
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\SectionNode", "nodeSrc", array (
				'alias' => 'nNodeSrc' 
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\SectionNode", "nodeDst", array (
				'alias' => 'nNodeDst' 
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\SectionNode", "nodeTrc", array (
				'alias' => 'nNodeTrc' 
		) );
	}
	public function beforeValidationOnCreate() {
		$this->tsCreate = date ( "Y-m-d H:i:s" );
		$this->tsUpdate = date ( "Y-m-d H:i:s" );
	}
	public function beforeValidationOnUpdate() {
		$this->tsUpdate = date ( "Y-m-d H:i:s" );
	}
	public function columnMap() {
		return array (
				'id' => 'id',
				'node_calc_id' => 'nodeCalcId',
				'node_spec_id' => 'nodeSpecId',
				'scheme_id' => 'schemeId',
				'map_point_id' => 'mapPointId',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate' 
		);
	}
	
	/**
	 * Get node in defined output structure.
	 *
	 * @param Node $node        	
	 * @return array
	 */
	public static function getNode(Node $node) {
		return array (
				"calc" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/nodes/%s/calc/", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id 
						) ) 
				),
				"spec" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/nodes/%s/spec/", Common::API_VERSION_V1, $node->id ), array (
								MG::KEY_ID => $node->id 
						) ) 
				),
				"scheme" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $node->schemeId ), array (
								MG::KEY_ID => $node->schemeId 
						) ) 
				),
				"mapPoint" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $node->mapPointId ), array (
								MG::KEY_ID => $node->mapPointId 
						) ) 
				) 
		);
	}
}
