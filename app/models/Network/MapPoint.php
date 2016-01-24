<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class MapPoint extends Model {
	
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
	public function initialize() {
		$this->setConnectionService ( 'networkDb' );
		
		$this->belongsTo ( "nodeId", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'node' 
		) );
		$this->belongsTo ( "schemeId", "CpdnAPI\Models\Network\Scheme", "id", array (
				'alias' => 'scheme' 
		) );
		$this->hasOne ( "id", "CpdnAPI\Models\Network\Node", "mapPointId", array (
				'alias' => 'oNode' 
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Path", "srcMapPointId", array (
				'alias' => 'nSrcMapPointId' 
		) );
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Path", "dstMapPointId", array (
				'alias' => 'nDstMapPointId' 
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
	
	/**
	 * Get map point in defined output structure.
	 *
	 * @param MapPoint $point        	
	 * @return array
	 */
	public static function getMapPoint(MapPoint $point) {
		return array (
				"gps" => array (
						"altitude" => $point->gpsAltitude,
						"latitude" => $point->gpsLatitude,
						"longitude" => $point->gpsLongitude 
				),
				"node" => (empty ( $point->nodeId ) ? null : array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $point->nodeId ), array (
								MG::KEY_ID => $point->nodeId 
						) ) 
				)),
				"scheme" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $point->schemeId ), array (
								MG::KEY_ID => $point->schemeId 
						) ) 
				),
				"x" => $point->x,
				"y" => $point->y 
		);
	}
}
