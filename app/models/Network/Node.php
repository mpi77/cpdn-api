<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Node extends Model {
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
	public $node_calc_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_spec_id;
	
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
	public $map_point_id;
	
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
