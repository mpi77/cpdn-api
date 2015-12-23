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
	public $scheme_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $profile_id;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $ts_from;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $ts_to;
	
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
}
