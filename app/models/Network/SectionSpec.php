<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class SectionSpec extends Model {
	
	const TYPE_LINE = "line";
	const TYPE_TRANSFORMER = "transformer";
	const TYPE_TRANSFORMER_W3 = "transformerW3";
	const TYPE_REACTOR = "reactor";
	const TYPE_SWITCH = "switch";
	
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
	public $status;
	
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
				'type' => 'type',
				'status' => 'status',
				'label' => 'label',
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
	
	/**
	 * Get section spec in defined output structure.
	 *
	 * @param SectionSpec $spec        	
	 * @return array
	 */
	public static function getSpec(SectionSpec $spec) {
		return array (
				"type" => $spec->type,
				"label" => $spec->label,
				"conductance" => $spec->conductance,
				"status" => $spec->status,
				"susceptance" => $spec->susceptance,
				"current" => array (
						"max" => $spec->currentMax,
						"noLoad" => $spec->currentNoload 
				),
				"reactance" => array (
						"ratio" => $spec->reactanceRatio,
						"value" => $spec->reactanceValue 
				),
				"resistance" => array (
						"ratio" => $spec->resistanceRatio,
						"value" => $spec->resistanceValue 
				),
				"losses" => array (
						"noLoad" => $spec->lossesNoload,
						"short" => array (
								"ab" => $spec->lossesShortAb,
								"ac" => $spec->lossesShortAc,
								"bc" => $spec->lossesShortBc 
						) 
				),
				"power" => array (
						"rated" => array (
								"ab" => $spec->powerRatedAb,
								"ac" => $spec->powerRatedAc,
								"bc" => $spec->powerRatedBc 
						) 
				),
				"voltage" => array (
						"pri" => array (
								"actual" => $spec->voltagePriActual,
								"rated" => $spec->voltagePriRated 
						),
						"sec" => array (
								"actual" => $spec->voltageSecActual,
								"rated" => $spec->voltageSecRated 
						),
						"trc" => array (
								"actual" => $spec->voltageTrcActual,
								"rated" => $spec->voltageTrcRated 
						),
						"short" => array (
								"ab" => $spec->voltageShortAb,
								"ac" => $spec->voltageShortAc,
								"bc" => $spec->voltageShortBc 
						) 
				) 
		);
	}
}
