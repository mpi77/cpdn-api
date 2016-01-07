<?php

namespace CpdnAPI\Utils;

class Expandable {
	const URL_QUERY_PARAM = "e";
	/**
	 * Builds array based data for expandable feature from query part of URL.
	 * This method is better to use with several expandable fields.
	 * It builds array of valid requested and expandable fields.
	 * This array is later used to walk through and find same fields while controller processing.
	 *
	 * @param string $e
	 *        	string from "e" param from query part of URL
	 *        	general example: (filed1,field2.field3,f4.f5.f6)
	 * @param array $valid_fields        	
	 * @return array
	 */
	public static function buildExpandableFields($e, $valid_fields = array()) {
		$r = array ();
		if (is_string ( $e ) && mb_strlen ( $e ) > 0) {
			// remove opening and closing brackets
			$e = mb_strcut ( $e, 1, mb_strlen ( $e ) - 2 );
			
			$e_fields = explode ( ",", $e );
			foreach ( $e_fields as $e_field ) {
				if (in_array ( $e_field, $valid_fields )) {
					$r [] = $e_field;
				}
			}
		}
		return $r;
	}
	public static function buildExpandableTree() {
	}
}