<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Permission extends Model {
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
	
	public function columnMap() {
		return array (
				'id' => 'id',
				'scheme_id' => 'schemeId',
				'profile_id' => 'profileId',
				'ts_from' => 'tsFrom',
				'ts_to' => 'tsTo',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
