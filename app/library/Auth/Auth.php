<?php

namespace CpdnAPI\Auth;

use Phalcon\Mvc\User\Component;
use Phalcon\Http\Request;
use CpdnAPI\Models\OAuth\AccessToken;

class Auth extends Component {
	private $accessToken = null;
	private $userId = null;
	
	/**
	 * Check a request authorization.
	 * 
	 * @param Phalcon\Http\Request $request
	 * @return boolean
	 */
	public function isAuthorized(Request $request) {
		$r = false;
		
		$bearer = $request->getHeader ( "Authorization" );
		$get = $request->getQuery("access_token");
		
		$token = "";
		
		if(!empty($bearer) && preg_match ( "/^((B|b)earer)\s(\S+)$/", $bearer, $m ) !== false){
			$token = $m[3];
		}
		
		if(!empty($get)){
			$token = $get;
		}
		
		if (! empty ( $token )) {
			$access_token = AccessToken::findFirst ( array (
					"accessToken = :token: AND :time: <= UNIX_TIMESTAMP(expires)",
					"bind" => array (
							"token" => $token,
							"time" => time ()
					)
			) );
				
			if ($access_token) {
				$this->userId = (int) $access_token->userId;
				$r = true;
			}
		}
		
		return $r;
	}
	
	/**
	 * Get userId from existing and valid access token.
	 *
	 * @return integer or null
	 */
	public function getUserId() {
		return is_integer( $this->userId ) ? $this->userId : null;
	}
	
	/**
	 * Set access token.
	 *
	 * @return void
	 */
	public function setAccessToken($token) {
		$this->accessToken = $token;
	}
	
	/**
	 * Get access token.
	 * 
	 * @return string or null
	 */
	public function getAccessToken() {
		return is_string ( $this->accessToken ) ? $this->accessToken : null;
	}
}
