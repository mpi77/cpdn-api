<?php

namespace CpdnAPI\Auth;

use Phalcon\Mvc\User\Component;

class Auth extends Component {
	
	/**
	 * @return boolean
	 */
	public function isAuthorized() {
		return true;
	}
}
