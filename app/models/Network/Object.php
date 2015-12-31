<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Object extends Model {
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
	public $scheme_id;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $name;
	
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
		$this->hasMany ( "id", "CpdnAPI\Models\Network\ObjectMember", "object_id", array (
				'alias' => 'nobjectmembers' 
		) );
		$this->belongsTo ( "scheme_id", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme' 
		) );
	}
}
