<?php

namespace CpdnAPI\Utils;

use CpdnAPI\Utils\MetaGenerator as MG;

class ItemGenerator {
	public static function generate($queryUri, $metaArgs = array(), $bodyArgs = array()) {
		$r = array();
		$r[MG::KEY_META] = MG::generate ( $queryUri, $metaArgs);
		return array_merge ( $r, $bodyArgs );
	}
}