<?php

namespace CpdnAPI\Utils;

class Sortable {
	/**
	 * Builds orderBy data for Criteria::orderBy() method from query part of URL.
	 *
	 * @param string $s
	 *        	string from "s" param from query part of URL
	 *        	general example: (filed)
	 * @param array $valid_fields        	
	 * @return string|false
	 */
	public static function buildCriteriaOrderByParams($s, $valid_fields = array()) {
		if (is_string ( $s ) && mb_strlen ( $s ) > 0) {
			// remove opening and closing brackets
			$s = mb_strcut ( $s, 1, mb_strlen ( $s ) - 2 );
			
			if (array_key_exists ( $s, $valid_fields )) {
				return $s;
			}
		}
		return false;
	}
}