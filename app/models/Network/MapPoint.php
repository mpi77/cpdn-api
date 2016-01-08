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
	public $schemeId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeId;
	
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
	public $gpsLatitude;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $gpsLongitude;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $gpsAltitude;
	
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
	
	public function columnMap() {
		return array (
				'id' => 'id',
				'scheme_id' => 'schemeId',
				'node_id' => 'nodeId',
				'x' => 'x',
				'y' => 'y',
				'gps_latitude' => 'gpsLatitude',
				'gps_longitude' => 'gpsLongitude',
				'gps_altitude' => 'gpsAltitude',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
