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
	public $nodeSrc;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeDst;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeTrc;
	
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
		$this->hasOne ( "id", "CpdnAPI\Models\Network\Section", "sectionNodeId", array (
				'alias' => 'section'
		) );
		
		$this->belongsTo ( "nodeSrc", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodeSrc'
		) );
		$this->belongsTo ( "nodeDst", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodeDst'
		) );
		$this->belongsTo ( "nodeTrc", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodeTrc'
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
				'node_src' => 'nodeSrc',
				'node_dst' => 'nodeDst',
				'node_trc' => 'nodeTrc',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
