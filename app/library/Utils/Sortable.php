<?php

namespace CpdnAPI\Utils;

class Sortable {
	const URL_QUERY_PARAM = "s";
	const SORT_ASC = "asc";
	const SORT_DESC = "desc";
	
	/**
	 * Builds orderBy data for Criteria::orderBy() method from query part of URL.
	 *
	 * Example:
	 * $query = Criteria::fromInput ( $this->di, 'CpdnAPI\Models\Network\Scheme', Searchable::buildCriteriaFromInputParams($this->request->get ( "q" ),$this->validFields) );
	 * if ($s = Sortable::buildCriteriaOrderByParams($this->request->get ( "s" ),$this->validFields)) {
	 * $query->orderBy ( $s );
	 * }
	 * $schemes = Scheme::find ( $query->getParams () );
	 * End of example;
	 *
	 * @param string $s
	 *        	string from "s" param from query part of URL
	 *        	general example: (filed)
	 * @param array $valid_fields
	 * 			each value is a field name        	
	 * @return string|false
	 */
	public static function buildCriteriaOrderByParams($s, $valid_fields = array()) {
		if (is_string ( $s ) && mb_strlen ( $s ) > 0) {
			// remove opening and closing brackets
			$s = mb_strcut ( $s, 1, mb_strlen ( $s ) - 2 );
			
			if (in_array ( $s, $valid_fields )) {
				return $s;
			}
		}
		return false;
	}
	
	/**
	 * Builds orderBy data for QueryBuilder::orderBy() method from query part of URL.
	 *
	 * @param string $s
	 *        	string from "s" param from query part of URL
	 *        	general example: (filed1,field2:asc,field3:desc)
	 * @param array $valid_fields
	 * 			each value is a field name        	
	 * @return string|false
	 */
	public static function buildQueryBuilderOrderByParams($s, $valid_fields = array()) {
		if (is_string ( $s ) && mb_strlen ( $s ) > 0) {
			// remove opening and closing brackets
			$s = mb_strcut ( $s, 1, mb_strlen ( $s ) - 2 );
			
			$r = array ();
			
			$s_fields = explode ( ",", $s );
			foreach ( $s_fields as $s_field ) {
				if (mb_strpos ( $s_field, ":" ) > 0) {
					$ss = explode ( ":", $s_field );
					$field = $ss [0];
					$direction = $ss [1];
					
					if (in_array ( $field, $valid_fields ) && ($direction == self::SORT_ASC || $direction == self::SORT_DESC)) {
						$r [] = sprintf ( "%s %s", $field, $direction );
					}
				} else {
					if (in_array ( $s_field, $valid_fields )) {
						$r [] = sprintf ( "%s %s", $s_field, self::SORT_ASC );
					}
				}
			}
			if (empty ( $r )) {
				return false;
			} else {
				return implode ( ",", $r );
			}
		}
		return false;
	}
}