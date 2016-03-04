<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class NodeSpec extends Model {
	
	const TYPE_POWER = "power";
	const TYPE_CONSUMPTION = "consumption";
	const TYPE_TURBO_GEN = "turbogen";
	const TYPE_HYDRO_GEN = "hydrogen";
	const TYPE_SUPERIOR_SYSTEM = "superiorSystem";
	
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
	public $type;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $label;
	
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
	public function initialize() {
		$this->setConnectionService ( 'networkDb' );
		
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
				'type' => 'type',
				'label' => 'label',
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
	
	/**
	 * Get node spec in defined output structure.
	 *
	 * @param NodeSpec $spec        	
	 * @return array
	 */
	public static function getSpec(NodeSpec $spec) {
		return array (
				"type" => $spec->type,
				"label" => $spec->label,
				"cosFi" => $spec->cosFi,
				"mi" => $spec->mi,
				"lambda" => array (
						"max" => $spec->lambdaMax,
						"min" => $spec->lambdaMin 
				),
				"power" => array (
						"active" => $spec->powerActive,
						"installed" => $spec->powerInstalled,
						"rated" => $spec->powerRated,
						"reactive" => $spec->powerReactive 
				),
				"reactance" => array (
						"longitudinal" => $spec->reactanceLongitudinal,
						"transverse" => $spec->reactanceTransverse
				),
				"voltage" => array (
						"level" => $spec->voltageLevel,
						"phase" => $spec->voltagePhase,
						"rated" => $spec->voltageRated,
						"value" => $spec->voltageValue 
				) 
		);
	}
}
