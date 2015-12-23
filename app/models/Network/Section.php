<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class Section extends Model {
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
	public $section_calc_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $section_spec_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $section_node_id;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $scheme_id;
	
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
