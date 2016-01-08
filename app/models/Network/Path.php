<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Path extends Model {
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
}
