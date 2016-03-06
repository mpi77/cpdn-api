<?php

namespace Tests\Utils;

use CpdnAPI\Utils\Expandable;

class ExpandableTest extends \UnitTestCase {
	public function testSingleFieldOk() {
		$r = Expandable::buildExpandableFields("(field1)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array("field1") );
		
		$r = Expandable::buildExpandableFields("(field1.field2)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array("field1.field2") );
	}
	
	public function testMultipleFieldsOk() {
		$r = Expandable::buildExpandableFields("(field1,field3)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array("field1", "field3") );
	
		$r = Expandable::buildExpandableFields("(field1.field2,field3)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array("field1.field2", "field3") );
	}
	
	public function testWrongInputFormat() {
		$r = Expandable::buildExpandableFields("", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array() );
	
		$r = Expandable::buildExpandableFields("()", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array() );
		
		$r = Expandable::buildExpandableFields("(,)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array() );
		
		$r = Expandable::buildExpandableFields("(filed1 - field3)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array() );
		
		$r = Expandable::buildExpandableFields("(filed1.field3.field2)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array() );
	}
	
	public function testUnknownFields() {
		$r = Expandable::buildExpandableFields("(field4)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array() );
		
		$r = Expandable::buildExpandableFields("(field4,field1)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array("field1") );
		
		$r = Expandable::buildExpandableFields("(field1,field4)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array("field1") );
		
		$r = Expandable::buildExpandableFields("(field1,field4,field3)", array("field1", "field1.field2", "field3"));
		$this->assertEquals ( $r, array("field1", "field3") );
	}
}