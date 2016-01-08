<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class SectionSpec extends Model {
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
	public $status;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $resistanceValue;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $resistanceRatio;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactanceValue;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactanceRatio;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $conductance;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $susceptance;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltagePriActual;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltagePriRated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageSecActual;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageSecRated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageTrcActual;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageTrcRated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageShortAb;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageShortAc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltageShortBc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lossesShortAb;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lossesShortAc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lossesShortBc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerRatedAb;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerRatedAc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerRatedBc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $currentNoload;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lossesNoload;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $currentMax;
	
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
		$this->setConnectionService ( 'networkDb' );
		
		$this->hasOne ( "id", "CpdnAPI\Models\Network\Section", "sectionSpecId", array (
				'alias' => 'section'
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
				'status' => 'status',
				'resistance_value' => 'resistanceValue',
				'resistance_ratio' => 'resistanceRatio',
				'reactance_value' => 'reactanceValue',
				'reactance_ratio' => 'reactanceRatio',
				'conductance' => 'conductance',
				'susceptance' => 'susceptance',
				'voltage_pri_actual' => 'voltagePriActual',
				'voltage_pri_rated' => 'voltagePriRated',
				'voltage_sec_actual' => 'voltageSecActual',
				'voltage_sec_rated' => 'voltageSecRated',
				'voltage_trc_actual' => 'voltageTrcActual',
				'voltage_trc_rated' => 'voltageTrcRated',
				'voltage_short_ab' => 'voltageShortAb',
				'voltage_short_ac' => 'voltageShortAc',
				'voltage_short_bc' => 'voltageShortBc',
				'losses_short_ab' => 'lossesShortAb',
				'losses_short_ac' => 'lossesShortAc',
				'losses_short_bc' => 'lossesShortBc',
				'power_rated_ab' => 'powerRatedAb',
				'power_rated_ac' => 'powerRatedAc',
				'power_rated_bc' => 'powerRatedBc',
				'current_noload' => 'currentNoload',
				'losses_noload' => 'lossesNoload',
				'current_max' => 'currentMax',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
