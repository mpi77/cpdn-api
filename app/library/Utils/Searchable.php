<?php

namespace CpdnAPI\Utils;

class Searchable {
	/**
	 * Builds searchable data for Criteria::fromInput() method from query part of URL.
	 *
	 * @param string $q
	 *        	string from "q" param from query part of URL
	 *        	general example: (filed=value;field2=value2)
	 * @param array $valid_fields
	 *        	format of an array: string_field_name => string_value_pattern
	 * @return array
	 */
	public static function buildCriteriaFromInputParams($q, $valid_fields = array()) {
		$filter = array ();
		if (is_string ( $q ) && mb_strlen ( $q ) > 0) {
			
			// remove opening and closing brackets + split string by ";" separator
			$q = mb_strcut ( $q, 1, mb_strlen ( $q ) - 2 );
			$q_fields = explode ( ";", $q );
			
			foreach ( $q_fields as $q_field ) {
				$qq = explode ( "=", $q_field );
				$key = $qq [0];
				$value = $qq [1];
				
				// validate key and value of one searchable argument
				if (array_key_exists ( $key, $valid_fields ) && preg_match ( $valid_fields [$key], $value ) === 1) {
				//if (array_key_exists ( $key, $valid_fields ) && is_string ( $value ) && mb_strlen ( $value ) > 0) {
					$filter [$key] = $value;
				}
			}
		}
		return $filter;
	}
}