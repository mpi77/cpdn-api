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
	public $src_map_point_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $dst_map_point_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $section_id;
	
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
