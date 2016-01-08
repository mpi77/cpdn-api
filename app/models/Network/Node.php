<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Node extends Model {
	public function initialize() {
		$this->setConnectionService ( 'networkDb' );
	}
	
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
	
	/**
	 * Initializer method for model.
	 */
	public function initialize() {
		$this->hasMany ( "id", "CpdnAPI\Models\Network\ObjectMember", "node_id", array (
				'alias' => 'nobjectmember' 
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\SectionNode", "node_src", array (
				'alias' => 'nnodesrc'
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\SectionNode", "node_dst", array (
				'alias' => 'nnodedst'
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\SectionNode", "node_trc", array (
				'alias' => 'nnodetrc'
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
