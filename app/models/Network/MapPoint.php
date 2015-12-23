<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class MapPoint extends Model {
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
	public $scheme_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $x;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $y;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $gps_latitude;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $gps_longitude;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $gps_altitude;
	
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
