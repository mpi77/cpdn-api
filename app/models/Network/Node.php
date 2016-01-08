<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

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
}
