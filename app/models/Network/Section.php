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
	public $section_calc_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $section_spec_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $section_node_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $scheme_id;
	
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
	
	/**
	 * Initializer method for model.
	 */
	public function initialize() {
		
		$this->belongsTo ( "scheme_id", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme'
		) );
		$this->belongsTo ( "section_spec_id", "CpdnAPI\Models\Network\SectionSpec", "id", array (
				'alias' => 'spec'
		) );
		$this->belongsTo ( "section_calc_id", "CpdnAPI\Models\Network\SectionCalc", "id", array (
				'alias' => 'calc'
		) );
		$this->belongsTo ( "section_node_id", "CpdnAPI\Models\Network\SectionNode", "id", array (
				'alias' => 'node'
		) );
	}
}
