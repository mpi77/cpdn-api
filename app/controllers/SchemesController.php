<?php

namespace CpdnAPI\Controllers;

use Phalcon\Http\Response;
use CpdnAPI\Models\Network\Scheme;

class SchemesController extends ControllerBase {
	public function initialize() {
		$this->view->disable ();
	}
	
	public function helloAction() {
		$response = new Response ();
		$response->setStatusCode ( 200, "OK" );

		$schemes = Scheme::find ();

		$r = array();
		foreach ($schemes as $s) {
			$r[] = array($s->id, $s->name);
		}

		$response->setJsonContent ( $r );
		return $response;
	}
}