<?php

namespace CpdnAPI\Utils;

class Common {
	const API_VERSION_V1 = "v1";
	
	public static function getApiVersions(){
		return array(self::API_VERSION_V1);
	}
	
	public static function isValidApiVersion($version) {
		return in_array($version, self::getApiVersions());
	}
}