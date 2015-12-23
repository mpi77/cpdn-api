<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class NodeCalc extends Model {
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
	public $load_active;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $load_reactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_drop_kv;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_drop_proc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_phase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_value;
	
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
