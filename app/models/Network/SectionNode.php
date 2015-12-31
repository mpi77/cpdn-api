<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class SectionNode extends Model {
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
	public $node_src;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_dst;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_trc;
	
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
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Section", "section_node_id", array (
				'alias' => 'nsection'
		) );
		
		$this->belongsTo ( "node_src", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodesrc'
		) );
		$this->belongsTo ( "node_dst", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodedst'
		) );
		$this->belongsTo ( "node_trc", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodetrc'
		) );
	}
}
