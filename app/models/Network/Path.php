<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Path extends Model {
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
	public $srcMapPointId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $dstMapPointId;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $sectionId;
	
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
		
		$this->belongsTo ( "srcMapPointId", "CpdnAPI\Models\Network\MapPoint", "id", array (
				'alias' => 'srcMapPoint'
		) );
		$this->belongsTo ( "dstMapPointId", "CpdnAPI\Models\Network\MapPoint", "id", array (
				'alias' => 'dstMapPoint'
		) );
		$this->belongsTo ( "sectionId", "CpdnAPI\Models\Network\Section", "id", array (
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
				'src_map_point_id' => 'srcMapPointId',
				'dst_map_point_id' => 'dstMapPointId',
				'section_id' => 'sectionId',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
	
	/**
	 * Get path in defined output structure.
	 *
	 * @param Path $path
	 * @return array
	 */
	public static function getPath(Path $path) {
		return array (
				"mapPoint" => array(
						"dst" => array(
								MG::KEY_META => MG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $path->dstMapPointId ), array (
										MG::KEY_ID => $path->dstMapPointId
						) )),
						"src" => array(
								MG::KEY_META => MG::generate ( sprintf ( "/%s/mapPoints/%s/", Common::API_VERSION_V1, $path->srcMapPointId ), array (
										MG::KEY_ID => $path->srcMapPointId
								) )
						)
				),
				"section" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/sections/%s/", Common::API_VERSION_V1, $path->sectionId ), array (
								MG::KEY_ID => $path->sectionId
						) )
				)
		);
	}
}
