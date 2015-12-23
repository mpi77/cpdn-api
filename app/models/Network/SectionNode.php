<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class SectionNode extends Model {
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
	public $node_src;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_dst;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $node_trc;
	
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
