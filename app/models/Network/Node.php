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
	public $node_calc_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_spec_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $scheme_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $map_point_id;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $ts_create;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $ts_update;
	
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
		
		$this->belongsTo ( "scheme_id", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme'
		) );
		$this->belongsTo ( "node_spec_id", "CpdnAPI\Models\Network\NodeSpec", "id", array (
				'alias' => 'spec'
		) );
		$this->belongsTo ( "node_calc_id", "CpdnAPI\Models\Network\NodeCalc", "id", array (
				'alias' => 'calc'
		) );
	}
}
