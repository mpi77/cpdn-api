<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Scheme extends Model {	
	/**
	 *
	 * @var integer
	 *
	 */
	public $id;
	
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
	public $description;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $version;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $lock;
	
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
		
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Node", "schemeId", array (
				'alias' => 'nNode'
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Section", "schemeId", array (
				'alias' => 'nSection'
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Object", "schemeId", array (
				'alias' => 'nObject'
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\MapPoint", "schemeId", array (
				'alias' => 'nMapPoint'
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Permission", "schemeId", array (
				'alias' => 'nPermission'
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
				'name' => 'name',
				'description' => 'description',
				'version' => 'version',
				'lock' => 'lock',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate' 
		);
	}
}
