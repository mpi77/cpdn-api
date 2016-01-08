<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class ObjectMember extends Model {
	public function initialize() {
		$this->setConnectionService ( 'networkDb' );
	}
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $objectId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeId;
	
	/**
	 * Initializer method for model.
	 */
	public function initialize() {
		$this->belongsTo ( "objectId", "CpdnAPI\Models\Network\Object", "id", array (
				'alias' => 'object' 
		) );
		$this->belongsTo ( "nodeId", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'node' 
		) );
	}
	
	public function columnMap() {
		return array (
				'object_id' => 'objectId',
				'node_id' => 'nodeId'
		);
	}
}
