<?php
namespace CpdnAPI\Models\Background;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Task extends Model 
{
	const STATUS_PREPARING = "preparing";
	const STATUS_NEW = "new";
	const STATUS_WORKING = "working";
	const STATUS_COMPLETE = "complete";
    
    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var string
     *
     */
    public $executorId;

    /**
     * @var integer
     *
     */
    public $schemeId;

    /**
     * @var integer
     *
     */
    public $profileId;

    /**
     * @var string
     *
     */
    public $status;

    /**
     * @var integer
     *
     */
    public $priority;

    /**
     * @var string
     *
     */
    public $command;

    /**
     * @var string
     *
     */
    public $result;

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

    /**
     * @var string
     *
     */
    public $tsReceive;

    /**
     * @var string
     *
     */
    public $tsExecute;

    public function initialize() {
    	$this->setConnectionService ( 'backgroundDb' );
    	
    	$this->belongsTo ( "executorId", "CpdnAPI\Models\Background\Executor", "id", array (
    			'alias' => 'executor'
    	) );
    	$this->belongsTo ( "schemeId", "CpdnAPI\Models\Network\Scheme", "id", array (
    			'alias' => 'scheme'
    	) );
    	$this->belongsTo ( "profileId", "CpdnAPI\Models\IdentityProvider\Profile", "id", array (
    			'alias' => 'profile'
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
    			'executor_id' => 'executorId',
    			'scheme_id' => 'schemeId',
    			'profile_id' => 'profileId',
    			'status' => 'status',
    			'priority' => 'priority',
    			'command' => 'command',
    			'result' => 'result',
    			'ts_create' => 'tsCreate',
    			'ts_update' => 'tsUpdate',
    			'ts_receive' => 'tsReceive',
    			'ts_execute' => 'tsExecute'
    	);
    }
    
    /**
     * Get task in defined output structure.
     *
     * @param Task $task
     * @return array
     */
    public static function getTask(Task $task) {
    	return array (
    			"executor" => array (
    					MG::KEY_META => MG::generate ( sprintf ( "/%s/executors/%s/", Common::API_VERSION_V1, $task->executorId ), array (
    							MG::KEY_ID => $task->executorId
    					) )
    			),
    			"user" => array (
    					MG::KEY_META => MG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $task->profileId ), array (
    							MG::KEY_ID => $task->profileId
    					) )
    			),
    			"scheme" => array (
    					MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $task->schemeId ), array (
    							MG::KEY_ID => $task->schemeId
    					) )
    			),
    			"status" => $task->status,
    			"priority" => $task->priority,
    			"command" => $task->command,
    			"result" => $task->result
    	);
    }
}
