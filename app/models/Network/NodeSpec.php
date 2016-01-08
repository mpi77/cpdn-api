<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class NodeSpec extends Model {
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
	public $powerInstalled;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerRated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageRated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageLevel;
	
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
	public $voltagePhase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $cosFi;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerActive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerReactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactanceTransverse;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactanceLongitudinal;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $mi;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lambdaMin;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lambdaMax;
	
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
		$this->hasOne ( "id", "CpdnAPI\Models\Network\Node", "nodeSpecId", array (
				'alias' => 'node' 
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
				'power_installed' => 'powerInstalled',
				'power_rated' => 'powerRated',
				'voltage_rated' => 'voltageRated',
				'voltage_level' => 'voltageLevel',
				'voltage_value' => 'voltageValue',
				'voltage_phase' => 'voltagePhase',
				'cos_fi' => 'cosFi',
				'power_active' => 'powerActive',
				'power_reactive' => 'powerReactive',
				'reactance_transverse' => 'reactanceTransverse',
				'reactance_longitudinal' => 'reactanceLongitudinal',
				'mi' => 'mi',
				'lambda_min' => 'lambdaMin',
				'lambda_max' => 'lambdaMax',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
