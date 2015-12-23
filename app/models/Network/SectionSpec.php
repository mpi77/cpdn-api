<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class SectionSpec extends Model {
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
	public $status;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $resistance_value;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $resistance_ratio;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactance_value;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactance_ratio;
	
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
	public $voltage_pri_actual;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_pri_rated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_sec_actual;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_sec_rated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_trc_actual;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_trc_rated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_short_ab;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_short_ac;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_short_bc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $losses_short_ab;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $losses_short_ac;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $losses_short_bc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_rated_ab;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_rated_ac;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_rated_bc;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $current_noload;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $losses_noload;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $current_max;
	
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
