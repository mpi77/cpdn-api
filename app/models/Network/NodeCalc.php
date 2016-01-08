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
	public $loadActive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $loadReactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageDropKv;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageDropProc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltagePhase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageValue;
	
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
	 * Initializer method for model.
	 */
	public function initialize() {
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Node", "nodeCalcId", array (
				'alias' => 'nnode'
		) );
	}
	
	public function columnMap() {
		return array (
				'id' => 'id',
				'load_active' => 'loadActive',
				'load_reactive' => 'loadReactive',
				'voltage_drop_kv' => 'voltageDropKv',
				'voltage_drop_proc' => 'voltageDropProc',
				'voltage_phase' => 'voltagePhase',
				'voltage_value' => 'voltageValue',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
