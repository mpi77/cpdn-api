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
				'name' => 'name',
				'description' => 'description',
				'version' => 'version',
				'lock' => 'lock',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate' 
		);
	}
}
