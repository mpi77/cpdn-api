<?php

namespace Tests\Utils;

use CpdnAPI\Utils\Sortable;

class SortableTest extends \UnitTestCase {
	public function testSingleFieldOk() {
		$r = Sortable::buildQueryBuilderOrderByParams("(field1)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field1 asc" );
	}
	
	public function testMultipleFieldsOk() {
		$r = Sortable::buildQueryBuilderOrderByParams("(field1,field2)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field1 asc,field2 asc" );
		
		$r = Sortable::buildQueryBuilderOrderByParams("(field1,field2:asc,field3:desc)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field1 asc,field2 asc,field3 desc" );
		
		$r = Sortable::buildQueryBuilderOrderByParams("(field1:desc,field2)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field1 desc,field2 asc" );
		
		$r = Sortable::buildQueryBuilderOrderByParams("(field3,field1)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field3 asc,field1 asc" );
	}
	
	public function testWrongInputFormat() {
		$r = Sortable::buildQueryBuilderOrderByParams("", array("field1", "field2", "field3"));
		$this->assertFalse ( $r );
		
		$r = Sortable::buildQueryBuilderOrderByParams("()", array("field1", "field2", "field3"));
		$this->assertFalse ( $r );
	
		$r = Sortable::buildQueryBuilderOrderByParams("(,)", array("field1", "field2", "field3"));
		$this->assertFalse ( $r);
	
		$r = Sortable::buildQueryBuilderOrderByParams("(field1 - field2)", array("field1", "field2", "field3"));
		$this->assertFalse ( $r );
	
		$r = Sortable::buildQueryBuilderOrderByParams("(field3.field1*field2)", array("field1", "field2", "field3"));
		$this->assertFalse ( $r );
	}
	
	public function testUnknownFields() {
		$r = Sortable::buildQueryBuilderOrderByParams("(field4)", array("field1", "field2", "field3"));
		$this->assertFalse ( $r );
	
		$r = Sortable::buildQueryBuilderOrderByParams("(field4,field2)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field2 asc" );
	
		$r = Sortable::buildQueryBuilderOrderByParams("(field1,field4,field2)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field1 asc,field2 asc" );
	
		$r = Sortable::buildQueryBuilderOrderByParams("(field1,field4)", array("field1", "field2", "field3"));
		$this->assertEquals ( $r, "field1 asc" );
	}
}