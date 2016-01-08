<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class SectionCalc extends Model {
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
	 * @var string
	 *
	 */
	public $currentSrcValue;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $currentSrcPhase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $currentSrcRatio;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $currentDstValue;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $currentDstPhase;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $currentDstRatio;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerSrcActive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerSrcReactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerDstActive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $powerDstReactive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lossesActive;
	
	/**
	 *
	 * @var string
	 *
	 */
	public $lossesReactive;
	
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
	
	/**
	 * Initializer method for model.
	 */
	public function initialize() {
		$this->hasMany ( "id", "CpdnAPI\Models\Network\Section", "sectionCalcId", array (
				'alias' => 'nsection'
		) );
	}
	
	public function columnMap() {
		return array (
				'id' => 'id',
				'current_src_value' => 'currentSrcValue',
				'current_src_phase' => 'currentSrcPhase',
				'current_src_ratio' => 'currentSrcRatio',
				'current_dst_value' => 'currentDstValue',
				'current_dst_phase' => 'currentDstPhase',
				'current_dst_ratio' => 'currentDstRatio',
				'power_src_active' => 'powerSrcActive',
				'power_src_reactive' => 'powerSrcReactive',
				'power_dst_active' => 'powerDstActive',
				'power_dst_reactive' => 'powerDstReactive',
				'losses_active' => 'lossesActive',
				'losses_reactive' => 'lossesReactive',
				'ts_create' => 'tsCreate',
				'ts_update' => 'tsUpdate'
		);
	}
}
