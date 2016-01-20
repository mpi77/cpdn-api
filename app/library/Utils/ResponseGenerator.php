<?php

namespace CpdnAPI\Utils;

use CpdnAPI\Utils\MetaGenerator as MG;

class ResponseGenerator {
	const KEY_CODE = "code";
	const KEY_DEV_MESSAGE = "devMessage";
	const KEY_USR_MESSAGE = "usrMessage";
	
	const S0 = 0;
	const S200 = 200;
	const S201 = 201;
	const S204 = 204;
	const S400 = 400;
	const S400_MISSING_FIELD = 4000;
	const S400_VALIDATION_FAILED = 4001;
	const S403 = 403;
	const S403_MODE_RESTRICTION = 4030;
	const S404 = 404;
	const S500 = 500;
	const S500_CRUD_ERROR = 5000;
	
	private static $codes = array(
			self::S0 => "Unknown message.",
			self::S200 => "[OK]",
			self::S201 => "[Created]",
			self::S204 => "[No Content]",
			self::S400 => "[Bad Request]",
			self::S400_MISSING_FIELD => "[Bad Request] Missing some required field or there is some invalid field in the request or in body.",
			self::S400_VALIDATION_FAILED => "[Bad Request] Validation failed.",
			self::S403 => "[Forbidden]",
			self::S403_MODE_RESTRICTION => "[Forbidden] You have not enought permission or permission mode to access resource.",
			self::S404 => "[Not Found]",
			self::S500 => "[Internal Server Error]",
			self::S500_CRUD_ERROR => "[Internal Server Error] Unable to create/read/update/delete data in storage."
	);
	
	public static function getCodes(){
		return self::$codes;
	}
	
	public static function getCodeMessage($code){
		return self::$codes[$code];
	}
	
	public static function generateContent($queryUri, $code = 0, $devMessage = "", $args = array()) {
		$r = array ();
		$r [MG::KEY_META] = MG::generate ( $queryUri );
		$r [self::KEY_CODE] = $code;
		$r [self::KEY_USR_MESSAGE] = self::getCodeMessage($code);
		$r [self::KEY_DEV_MESSAGE] = $devMessage;
		return array_merge ( $r, $args );
	}
}