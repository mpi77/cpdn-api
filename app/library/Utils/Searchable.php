<?php

namespace CpdnAPI\Utils;

class Searchable {
	const URL_QUERY_PARAM = "q";
	/**
	 * Builds searchable data for Criteria::fromInput() method from query part of URL.
	 *
	 * Example:
	 * $query = Criteria::fromInput ( $this->di, 'CpdnAPI\Models\Network\Scheme', Searchable::buildCriteriaFromInputParams($this->request->get ( "q" ),$this->validFields) );
	 * if ($s = Sortable::buildCriteriaOrderByParams($this->request->get ( "s" ),$this->validFields)) {
	 * $query->orderBy ( $s );
	 * }
	 * $schemes = Scheme::find ( $query->getParams () );
	 * End of example;
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
				$key = isset($qq [0]) ? $qq [0] : "";
				$value = isset($qq [1]) ? $qq [1] : "";
				
				// validate key and value of one searchable argument
				if (array_key_exists ( $key, $valid_fields ) && preg_match ( $valid_fields [$key], $value ) === 1) {
					// if (array_key_exists ( $key, $valid_fields ) && is_string ( $value ) && mb_strlen ( $value ) > 0) {
					$filter [$key] = $value;
				}
			}
		}
		return $filter;
	}
	
	/**
	 * Builds searchable data for QueryBuilder::where() method from query part of URL.
	 *
	 * @param string $q
	 *        	string from "q" param from query part of URL
	 *        	general example: (filed=value;field2=value2)
	 * @param array $valid_fields
	 *        	this array contains ALL fields (= required + optional)
	 *        	format of an array: array(string_field_name => string_value_pattern)
	 * @param array $required_fields
	 *        	this array contains ONLY REQUIRED fields (as values in array)
	 *        	format of an array: array(string_field_name,string_field_name2)
	 * @return array|false|null array in case of presence some valid fields
	 *         false in case of missing some required field or there is some invalid field
	 *         null in case of empty query string
	 */
	public static function buildQueryBuilderWhereParams($q, $valid_fields = array(), $required_fields = array()) {
		if (is_string ( $q ) && mb_strlen ( $q ) > 0) {
			$r = array (
					"conditions" => "",
					"bindParams" => array () 
			);
			
			// init $require array with all values from $required_fields and set them to FALSE
			$rq = array ();
			foreach ( $required_fields as $field ) {
				$rq [$field] = false;
			}
			
			// remove opening and closing brackets + split string by ";" separator
			$q = mb_strcut ( $q, 1, mb_strlen ( $q ) - 2 );
			$q_fields = explode ( ";", $q );
			
			foreach ( $q_fields as $q_field ) {
				$qq = explode ( "=", $q_field );
				$field = isset($qq [0]) ? $qq [0] : "";
				$value = isset($qq [1]) ? $qq [1] : "";
				
				// validate key and value of one searchable argument
				if (array_key_exists ( $field, $valid_fields ) && preg_match ( $valid_fields [$field], $value ) === 1) {
					$r ["conditions"] = empty ( $r ["conditions"] ) ? sprintf ( " %s = :%s: ", $field, $field ) : sprintf ( "%s AND %s = :%s: ", $r ["conditions"], $field, $field );
					$r ["bindParams"] [$field] = $value;
					
					// if field is reguired set this to TRUE to notify its presence
					if (array_key_exists( $field, $rq )) {
						$rq [$field] = true;
					}
				} else {
					// invalid field or invalid field value
					return false;
				}
			}
			if (empty ( $r ["conditions"] ) && empty ( $r ["bindParams"] )) {
				if (in_array ( false, $rq )) {
					return false;
				} else {
					return null;
				}
			} else {
				if (in_array ( false, $rq )) {
					return false;
				} else {
					return $r;
				}
			}
		} else {
			if (empty ( $required_fields )) {
				return null;
			} else {
				return false;
			}
		}
	}
}