<?php

namespace CpdnAPI\Models\OAuth;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class AccessToken extends Model {
	
	/**
	 *
	 * @var string
	 *
	 */
	public $accessToken;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $clientId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $userId;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $expires;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $scope;
	
	public function initialize() {
		$this->setConnectionService ( 'oauthDb' );
	}
	
	public function columnMap() {
		return array (
				'access_token' => 'accessToken',
				'client_id' => 'clientId',
				'user_id' => 'userId',
				'expires' => 'expires',
				'scope' => 'scope'
		);
	}
}
