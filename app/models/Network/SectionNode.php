<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class SectionNode extends Model {
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
	public $nodeSrc;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeDst;
	
	/**
	 *
	 * @var integer
	 *
	 */
	public $nodeTrc;
	
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
		
		$this->hasOne ( "id", "CpdnAPI\Models\Network\Section", "sectionNodeId", array (
				'alias' => 'section' 
		) );
		
		$this->belongsTo ( "nodeSrc", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodeSrc' 
		) );
		$this->belongsTo ( "nodeDst", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodeDst' 
		) );
		$this->belongsTo ( "nodeTrc", "CpdnAPI\Models\Network\Node", "id", array (
				'alias' => 'nodeTrc' 
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
				'node_src' => 'nodeSrc',
				'node_dst' => 'nodeDst',
				'node_trc' => 'nodeTrc',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate' 
		);
	}
	
	/**
	 * Get section nodes in defined output structure.
	 *
	 * @param SectionNode $nodes        	
	 * @return array
	 */
	public static function getNodes(SectionNode $nodes) {
		return array (
				"dst" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $nodes->nodeDst ), array (
								MG::KEY_ID => $nodes->nodeDst 
						) ) 
				),
				"src" => array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $nodes->nodeSrc ), array (
								MG::KEY_ID => $nodes->nodeSrc 
						) ) 
				),
				"trc" => (empty ( $nodes->nodeTrc ) ? null : array (
						MG::KEY_META => MG::generate ( sprintf ( "/%s/nodes/%s/", Common::API_VERSION_V1, $nodes->nodeTrc ), array (
								MG::KEY_ID => $nodes->nodeTrc 
						) ) 
				)) 
		);
	}
}
