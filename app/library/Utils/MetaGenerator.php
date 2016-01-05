<?php

namespace CpdnAPI\Utils;

class MetaGenerator {
	const KEY_META = "_meta";
	const KEY_HREF = "href";
	const KEY_MEDIA_TYPE = "mediaType";
	const KEY_ID = "id";
	public static function generate($queryUri, $args = array(), $protocol = "https", $baseUri = "api.cpdn.sd2.cz", $format = "application/json") {
		$r [self::KEY_HREF] = sprintf ( "%s://%s%s", $protocol, $baseUri, $queryUri );
		$r [self::KEY_MEDIA_TYPE] = $format;
		return array_merge ( $r, $args );
	}
}