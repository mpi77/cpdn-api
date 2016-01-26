<?php

namespace CpdnAPI\Acl;

use Phalcon\Mvc\User\Component;
use CpdnAPI\Models\Network\Permission;

class Acl extends Component {
	
	/**
	 * Check an acl permission to given scheme and user.
	 *
	 * @param integer $schemeId  
	 * @param string $mode
	 *        	minimal required mode (constants from Permission::MODE_*)
	 * @param integer $profileId
	 *        	optional param, if is null data will be automatically loaded from auth->getUserId()
	 * @return boolean
	 */
	public function check($schemeId, $mode, $profileId = null) {
		$r = false;
		
		if(empty($schemeId) || empty($mode)){
			return false;
		}
		
		if (is_null ( $profileId )) {
			$profileId = $this->auth->getUserId ();
			
			if (empty ( $profileId )) {
				return false;
			}
		}
		
		$perm = Permission::findFirst ( array (
				"schemeId = :schemeId: AND profileId = :profileId: AND :time: > UNIX_TIMESTAMP(tsFrom) AND :time: <= UNIX_TIMESTAMP(tsTo) AND mode LIKE :mode:",
				"bind" => array (
						"schemeId" => $schemeId,
						"profileId" => $profileId,
						"time" => time (),
						"mode" => sprintf ( "%%%s%%", $mode ) 
				) 
		) );
		
		if ($perm) {
			$r = true;
		}
		
		return $r;
	}
}
