<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Section extends Model {
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
	public function initialize() {
		$this->setConnectionService ( 'networkDb' );
		
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
	
	/**
	 * Get section in defined output structure.
	 *
	 * @param Section $section        	
	 * @return array
	 */
	public static function getSection(Section $section) {
		return array (
				"calc" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/sections/%s/calc/", Common::API_VERSION_V1, $section->id ), array (
								MG::KEY_ID => $section->id 
						) ) 
				),
				"spec" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/sections/%s/spec/", Common::API_VERSION_V1, $section->id ), array (
								MG::KEY_ID => $section->id 
						) ) 
				),
				"nodes" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/sections/%s/nodes/", Common::API_VERSION_V1, $section->id ), array (
								MG::KEY_ID => $section->id 
						) ) 
				),
				"scheme" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $section->schemeId ), array (
								MG::KEY_ID => $section->schemeId 
						) ) 
				) 
		);
	}
}
