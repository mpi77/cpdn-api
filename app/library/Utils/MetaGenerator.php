<?php

namespace CpdnAPI\Utils;

class MetaGenerator {
	
	const KEY_HREF = "href";
	const KEY_MEDIA_TYPE = "mediaType";
	const KEY_ID = "id";
	
	public static function generate($pathUri, $args = array(), $protocol = "https", $baseUri = "api.cpdn.sd2.cz/v1", $format = "application/json"){
		$r[self::KEY_HREF] = sprintf("%s://%s/%s",$protocol, $baseUri, $pathUri);
		$r[self::KEY_MEDIA_TYPE] = $format;
		return array_merge($r, $args);
	}
}