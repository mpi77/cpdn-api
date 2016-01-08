<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Section extends Model {
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
	public $sectionCalcId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $sectionSpecId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $sectionNodeId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $schemeId;
	
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
		
		$this->belongsTo ( "schemeId", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme'
		) );
		$this->belongsTo ( "sectionSpecId", "CpdnAPI\Models\Network\SectionSpec", "id", array (
				'alias' => 'spec'
		) );
		$this->belongsTo ( "sectionCalcId", "CpdnAPI\Models\Network\SectionCalc", "id", array (
				'alias' => 'calc'
		) );
		$this->belongsTo ( "sectionNodeId", "CpdnAPI\Models\Network\SectionNode", "id", array (
				'alias' => 'node'
		) );
		
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Path", "sectionId", array (
				'alias' => 'nPath'
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
				'section_calc_id' => 'sectionCalcId',
				'section_spec_id' => 'sectionSpecId',
				'section_node_id' => 'sectionNodeId',
				'scheme_id' => 'schemeId',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
