<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Object extends Model {
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
	public $schemeId;
	
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
	public $tsCreate;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $tsUpdate;
	
	public function initialize() {
		$this->setConnectionService ( 'networkDb' );
		
		$this->hasMany ( "id", "CpdnAPI\Models\Network\ObjectMember", "objectId", array (
				'alias' => 'nObjectMember' 
		) );
		$this->belongsTo ( "schemeId", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme' 
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
				'scheme_id' => 'schemeId',
				'name' => 'name',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
	
	/**
	 * Get object in defined output structure.
	 *
	 * @param Object $object
	 * @return array
	 */
	public static function getObject(Object $object) {
		return array (
				"name" => $object->name,
				"scheme" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $object->schemeId ), array (
								MG::KEY_ID => $object->schemeId
						) )
				)
		);
	}
}
