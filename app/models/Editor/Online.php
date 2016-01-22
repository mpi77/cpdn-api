<?php
namespace CpdnAPI\Models\Editor;

use Phalcon\Mvc\Model;
use CpdnAPI\Utils\Common;
use CpdnAPI\Utils\MetaGenerator as MG;

class Online extends Model 
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var integer
     *
     */
    public $profileId;

    /**
     * @var integer
     *
     */
    public $schemeId;

    /**
     * @var string
     *
     */
    public $ipAddress;

    /**
     * @var string
     *
     */
    public $userAgent;

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
    	$this->setConnectionService ( 'editorDb' );
    
    	$this->belongsTo ( "profileId", "CpdnAPI\Models\IdentityProvider\Profile", "id", array (
    			'alias' => 'profile'
    	) );
    	$this->belongsTo ( "schemeId", "CpdnAPI\Models\Network\Scheme", "id", array (
    			'alias' => 'scheme'
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
    			'profile_id' => 'profileId',
    			'scheme_id' => 'schemeId',
    			'ip_address' => 'ipAddress',
    			'user_agent' => 'userAgent',
    			'ts_create' => 'tsCreate',
    			'ts_update' => 'tsUpdate'
    	);
    }
    
    /**
     * Get online in defined output structure.
     *
     * @param Online $online
     * @return array
     */
    public static function getOnline(Online $online) {
    	return array (
    			"user" => array (
    					MG::KEY_META => MG::generate ( sprintf ( "/%s/users/%s/", Common::API_VERSION_V1, $online->profileId ), array (
    							MG::KEY_ID => $online->profileId
    					) )
    			),
    			"scheme" => (empty ( $online->schemeId ) ? null :array (
    					MG::KEY_META => MG::generate ( sprintf ( "/%s/schemes/%s/", Common::API_VERSION_V1, $online->schemeId ), array (
    							MG::KEY_ID => $online->schemeId
    					) )
    			)),
    			"ipAddress" => $online->ipAddress,
    			"userAgent" => $online->userAgent
    	);
    }
}
