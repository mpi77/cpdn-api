<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class SectionCalc extends Model {
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
	public $current_src_value;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $current_src_phase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $current_src_ratio;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $current_dst_value;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $current_dst_phase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $current_dst_ratio;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_src_active;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_src_reactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_dst_active;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_dst_reactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $losses_active;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $losses_reactive;
	
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
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Section", "section_calc_id", array (
				'alias' => 'nsection'
		) );
	}
}
