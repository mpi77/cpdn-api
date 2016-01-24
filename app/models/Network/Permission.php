<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Permission extends Model {	
	const MODE_READ = "r";
	const MODE_WRITE = "w";
	const MODE_EXECUTE = "x";
	const MODE_READ_WRITE = "rw";
	const MODE_READ_EXECUTE = "rx";
	const MODE_WRITE_EXECUTE = "wx";
	const MODE_READ_WRITE_EXECUTE = "rwx";
	
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
	 * @var integer
	 *
	 */
	public $profileId;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $mode;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $tsFrom;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $tsTo;
	
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
		
		$this->belongsTo ( "schemeId", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme' 
		) );
		
		$this->belongsTo ( "profileId", "CpdnAPI\Models\IdentityProvider\Profile", "id", array (
				'alias' => 'profile'
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
				'profile_id' => 'profileId',
				'mode' => 'mode',
				'ts_from' => 'tsFrom',
				'ts_to' => 'tsTo',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
	
	/**
	 * Get permission in defined output structure.
	 *
	 * @param Permission $permission
	 * @return array
	 */
	public static function getPermission(Permission $permission) {
		return array (
				"mode" => $permission->mode,
				"tsFrom" => $permission->tsFrom,
				"tsTo" => $permission->tsTo,
				"user" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $permission->profileId ), array (
								MG::KEY_ID => $permission->profileId
						) )
				),
				"scheme" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $permission->schemeId ), array (
								MG::KEY_ID => $permission->schemeId
						) )
				)
		);
	}
}
