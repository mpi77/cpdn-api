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
	public $object_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_id;
	
	/**
	 * Initializer method for model.
	 */
	public function initialize() {
		$this->belongsTo ( "object_id", "CpdnAPI\Models\Network\Object", "id", array (
				'alias' => 'object' 
		) );
		$this->belongsTo ( "node_id", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'node' 
		) );
	}
}
