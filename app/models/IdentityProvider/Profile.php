<?php

namespace CpdnAPI\Models\IdentityProvider;

use Phalcon\Mvc\Model;

class Profile extends Model {
	
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
	public $roleId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $contactId;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $nick;
	
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
		$this->setConnectionService ( 'idpDb' );
		
		$this->belongsTo ( "contactId", "CpdnAPI\Models\IdentityProvider\Contact", "id", array (
				'alias' => 'contact' 
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
				'role_id' => 'roleId',
				'contact_id' => 'contactId',
				'nick' => 'nick',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate' 
		);
	}
	
	/**
	 * Get profile in defined output structure.
	 *
	 * @param Profile $profile
	 * @return array
	 */
	public static function getProfile(Profile $profile) {
		return array (
				"contact" => Contact::getContact($profile->contact),
				"nick" => $profile->nick
		);
	}
}
