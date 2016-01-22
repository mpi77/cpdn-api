<?php

namespace CpdnAPI\Models\Editor;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Notification extends Model {
	
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
	public $profileId;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $title;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $content;
	
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
	 *
	 * @var string
	 *
	 */
	public $tsRead;
	public function initialize() {
		$this->setConnectionService ( 'editorDb' );
		
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
				'profile_id' => 'profileId',
				'title' => 'title',
				'content' => 'content',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate',
				'ts_read' => 'tsRead' 
		);
	}
	
	/**
	 * Get notification in defined output structure.
	 *
	 * @param Notification $notification        	
	 * @return array
	 */
	public static function getNotification(Notification $notification) {
		return array (
				"user" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $notification->profileId ), array (
								MG::KEY_ID => $notification->profileId 
						) ) 
				),
				"title" => $notification->title,
				"content" => $notification->content,
				"tsRead" => $notification->tsRead 
		);
	}
}
