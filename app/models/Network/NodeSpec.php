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
	public $power_installed;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_rated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_rated;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_level;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_value;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $voltage_phase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $cos_fi;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_active;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $power_reactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactance_transverse;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $reactance_longitudinal;
	
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
	public $lambda_min;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lambda_max;
	
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
