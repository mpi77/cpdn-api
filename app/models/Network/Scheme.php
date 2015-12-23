<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Scheme extends Model {
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
	public $ts_create;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $ts_update;
}
