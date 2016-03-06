<?php

namespace Tests\Utils;

use CpdnAPI\Utils\Searchable;

class SearchableTest extends \UnitTestCase {
	public function testSingleField() {
		$r = Searchable::buildQueryBuilderWhereParams ( "(field1=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/") );
		$e = array(
				"conditions" => " field1 = :field1: ",
				"bindParams" => array(
						"field1" => "value"
				)
		);
		$this->assertEquals ( $r, $e );
		
		// required field1
		$r = Searchable::buildQueryBuilderWhereParams ( "(field1=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$e = array(
				"conditions" => " field1 = :field1: ",
				"bindParams" => array(
						"field1" => "value"
				)
		);
		$this->assertEquals ( $r, $e );
		
		// required field1 but given field2
		$r = Searchable::buildQueryBuilderWhereParams ( "(field2=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$this->assertFalse ( $r );
	}
	
	public function testMultipleFields() {
		$r = Searchable::buildQueryBuilderWhereParams ( "(field1=value;field2=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/") );
		$e = array(
				"conditions" => " field1 = :field1:  AND field2 = :field2: ",
				"bindParams" => array(
						"field1" => "value",
						"field2" => "value"
				)
		);
		$this->assertEquals ( $r, $e );
	
		// required field1
		$r = Searchable::buildQueryBuilderWhereParams ( "(field1=value;field2=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$e = array(
				"conditions" => " field1 = :field1:  AND field2 = :field2: ",
				"bindParams" => array(
						"field1" => "value",
						"field2" => "value"
				)
		);
		$this->assertEquals ( $r, $e );
	
		// required field1 but given field2 and field3
		$r = Searchable::buildQueryBuilderWhereParams ( "(field2=value;field3=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$this->assertFalse ( $r );
	}
	
	public function testWrongInputFormat() {
		$r = Searchable::buildQueryBuilderWhereParams ( "", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertNull ( $r );
		
		$r = Searchable::buildQueryBuilderWhereParams ( "", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$this->assertFalse ( $r );
	
		$r = Searchable::buildQueryBuilderWhereParams ( "()", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertFalse ( $r );
		
		$r = Searchable::buildQueryBuilderWhereParams ( "()", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$this->assertFalse ( $r );
	
		$r = Searchable::buildQueryBuilderWhereParams ( "(,)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertFalse ( $r );
		
		$r = Searchable::buildQueryBuilderWhereParams ( "(,)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$this->assertFalse ( $r );
	
		$r = Searchable::buildQueryBuilderWhereParams ( "(field1;field2)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertFalse ( $r );
		
		$r = Searchable::buildQueryBuilderWhereParams ( "(field1;field2)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"), array("field1") );
		$this->assertFalse ( $r );
	}
	
	public function testUnknownFields() {
		$r = Searchable::buildQueryBuilderWhereParams ( "(field4)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertFalse ( $r );
	
		$r = Searchable::buildQueryBuilderWhereParams ( "(field4=value;field2=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertFalse ( $r );
		
		$r = Searchable::buildQueryBuilderWhereParams ( "(field2=value;field4=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertFalse ( $r );
		
		$r = Searchable::buildQueryBuilderWhereParams ( "(field2=value;field4=value;field1=value)", array ("field1" => "/value/", "field2" => "/value/", "field3" => "/value/"));
		$this->assertFalse ( $r );
		
	}
}