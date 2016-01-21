<?php
namespace CpdnAPI\Models\Background;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Executor extends Model 
{
	const STATUS_ONLINE = "online";
	const STATUS_OFFLINE = "offline";
	
    /**
     * @var string
     *
     */
    public $id;

    /**
     * @var string
     *
     */
    public $title;

    /**
     * @var string
     *
     */
    public $status;

    /**
     * @var string
     *
     */
    public $tsCreate;

    /**
     * @var string
     *
     */
    public $tsUpdate;

    public function initialize() {
    	$this->setConnectionService ( 'backgroundDb' );
    	
    	$this->hasMany ( "id", "CpdnAPI\Models\Background\Task", "executorId", array (
    			'alias' => 'nTask'
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
    			'title' => 'title',
    			'status' => 'status',
    			'ts_create' => 'tsCreate',
    			'ts_update' => 'tsUpdate'
    	);
    }
    
    /**
     * Get executor in defined output structure.
     *
     * @param Executor $executor
     * @return array
     */
    public static function getExecutor(Executor $executor) {
    	return array (
    			"title" => $executor->title,
    			"status" => $executor->status
    	);
    }
}
