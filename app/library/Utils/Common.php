<?php

namespace CpdnAPI\Utils;

class Common {
	const API_VERSION_V1 = "v1";
	
	const PATTERN_ISO_8601 = "/^(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)T(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d(?:Z|[+-][01]\d:[0-5]\d)$/";
	const PATTERN_TIMESTAMP = "/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/";
	const PATTERN_BOOLEAN = "/^(true|false)$/";
	const PATTERN_INT_BOOLEAN = "/^(0|1)$/";
	const PATTERN_DOUBLE = "/^[+-]?\d*\.\d+|^[+-]?\d+$/";
	const PATTERN_DOUBLE_OR_NULL = "/^$|^[+-]?\d*\.\d+|^[+-]?\d+$/";
	const PATTERN_UNSIGNED_INTEGER_WITH_ZERO = "/^\d+$/";
	const PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO = "/^[1-9][0-9]{0,9}$/";
	const PATTERN_UNSIGNED_INTEGER_WITHOUT_ZERO_OR_NULL = "/^$|^[1-9][0-9]{0,9}$/";
	const PATTERN_TEXT_OPTIONAL = "/^[\s\S]*$/";
	
	public static function getApiVersions(){
		return array(self::API_VERSION_V1);
	}
	
	public static function isValidApiVersion($version) {
		return in_array($version, self::getApiVersions());
	}
}