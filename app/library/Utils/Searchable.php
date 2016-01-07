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
				$key = $qq [0];
				$value = $qq [1];
				
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
	 *        	format of an array: string_field_name => string_value_pattern
	 * @return array|false
	 */
	public static function buildQueryBuilderWhereParams($q, $valid_fields = array()) {
		if (is_string ( $q ) && mb_strlen ( $q ) > 0) {
			$r = array (
					"conditions" => "",
					"bindParams" => array () 
			);
			
			// remove opening and closing brackets + split string by ";" separator
			$q = mb_strcut ( $q, 1, mb_strlen ( $q ) - 2 );
			$q_fields = explode ( ";", $q );
			
			foreach ( $q_fields as $q_field ) {
				$qq = explode ( "=", $q_field );
				$field = $qq [0];
				$value = $qq [1];
				
				// validate key and value of one searchable argument
				if (array_key_exists ( $field, $valid_fields ) && preg_match ( $valid_fields [$field], $value ) === 1) {
					$r ["conditions"] = empty ( $r ["conditions"] ) ? sprintf ( " %s = :%s: ", $field, $field ) : sprintf ( "%s AND %s = :%s: ", $r ["conditions"], $field, $field );
					$r ["bindParams"] [$field] = $value;
				}
			}
			if (empty ( $r ["conditions"] ) && empty ( $r ["bindParams"] )) {
				return false;
			} else {
				return $r;
			}
		}
		return false;
	}
}